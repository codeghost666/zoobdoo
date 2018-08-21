<?php

namespace Erp\AdminBundle\Controller;

use Erp\CoreBundle\EmailNotification\EmailNotificationFactory;
use Erp\PaymentBundle\PaySimple\Managers\PaySimpleManagerInterface;
use Erp\PaymentBundle\PaySimple\Models\PaySimpleModels\RecurringPaymentModel;
use Erp\PropertyBundle\Entity\Property;
use Erp\UserBundle\Entity\ProReport;
use Erp\UserBundle\Entity\ProRequest;
use Erp\UserBundle\Entity\User;
use Sonata\AdminBundle\Controller\CRUDController as BaseController;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class CRUDController extends BaseController
{
    /**
     * Send email to manager with invitation to complete profile
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \Erp\CoreBundle\Exception\UserNotFoundExceptionct
     */
    public function sentInviteAction()
    {
        /** @var $user \Erp\UserBundle\Entity\User */
        $user = $this->admin->getSubject();
        $isConfirmed = false;
        $isPending = false;

        if ($user) {
            $isConfirmed = $user->getStatus() == User::STATUS_NOT_CONFIRMED;
            $isPending = $user->getStatus() == User::STATUS_PENDING;
        }

        if (!$user || (!$isConfirmed && !$isPending)) {
            throw $this->createNotFoundException();
        }

        $emailParams = [
            'sendTo' => $user->getEmail(),
            'url'    => $this->generateUrl('fos_user_security_login', [], UrlGeneratorInterface::ABSOLUTE_URL),
        ];
        $emailType = EmailNotificationFactory::TYPE_MANAGER_COMPLETE_PROFILE;
        $isSent = $this->get('erp.core.email_notification.service')->sendEmail($emailType, $emailParams);

        if ($isSent) {
            $this->addFlash('sonata_flash_success', 'Email successfully send');
        } else {
            $this->addFlash('sonata_flash_error', 'An error occurred while sending a message. Please, try again later');
        }

        return $this->redirect($this->generateUrl('admin_erpuserbundle_managers_list'));
    }

    /**
     * Charge manager for Pro Request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function chargeManagerAction()
    {
        /** @var $proRequest \Erp\UserBundle\Entity\ProRequest */
        $proRequest = $this->admin->getSubject();
        if (!$proRequest || $proRequest->getStatus() !== ProRequest::STATUS_APPROVED) {
            throw $this->createNotFoundException();
        }

        $customer = $proRequest->getUser()->getPaySimpleCustomers()->first();
        if (!$customer) {
            throw $this->createNotFoundException();
        }

        $currentDate = new \DateTime();
        $accountId = $customer->getPrimaryType() === PaySimpleManagerInterface::CREDIT_CARD
            ? $customer->getCcId()
            : $customer->getBaId();
        $amount = $proRequest->getConsultantFee() + $proRequest->getServicingFee();
        $paymentModel = new RecurringPaymentModel();
        $paymentModel->setAmount($amount)
            ->setCustomer($customer)
            ->setStartDate(new \DateTime())
            ->setAccountId($accountId);

        $paymentResponse = $this->get('erp.users.user.service')->makeOnePayment($paymentModel);

        if (!$paymentResponse['status']) {
            $this->get('erp.payment.paysimple_service')->sendPaymentEmail($customer);
            $this->addFlash(
                'sonata_flash_ps_error',
                'An error occurred while charging.'
            );
            $proRequest->setApprovedDate($currentDate)->setStatus(ProRequest::STATUS_PAYMENT_ERROR);
        } else {
            $proRequest->setApprovedDate($currentDate)->setStatus(ProRequest::STATUS_PAYMENT_OK);
            if ($proRequest->getIsRefferal()) {
                $this->addToReport($proRequest, $currentDate);
            }
            $this->addFlash('sonata_flash_ps_success', 'Manager was charged successfully');
        }

        $em = $this->getDoctrine()->getEntityManager();
        $em->persist($proRequest);
        $em->flush();

        return $this->redirect($this->get('request')->headers->get('referer'));
    }

    /**
     * Delete tenant
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteTenantAction()
    {
        $this->deleteUser();

        return $this->redirect($this->generateUrl('admin_erpuserbundle_tenants_list'));
    }

    /**
     * Delete manager
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteManagerAction()
    {
        $this->deleteUser();

        return $this->redirect($this->generateUrl('admin_erpuserbundle_managers_list'));
    }

    /**
     * Disable Manager
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function disableManagerAction()
    {
        /** @var $user \Erp\UserBundle\Entity\User */
        $user = $this->admin->getSubject();
        if (!$user || !$user->isActive()) {
            throw $this->createNotFoundException();
        }

        $apiManager = $this->get('erp_stripe.entity.api_manager');
        $userService = $this->get('erp.users.user.service');

        $user->clearProperties();
        $userService->deactivateUser($user);

        $stripeCustomer = $user->getStripeCustomer();

        $em = $this->getDoctrine()->getManager();

        if ($stripeCustomer) {
            $stripeSubscription = $stripeCustomer->getStripeSubscription();

            if (!$stripeSubscription) {
                $this->addFlash(
                    'sonata_flash_error',
                    'The user does not have subscription'
                );
                return $this->redirect($this->get('request')->headers->get('referer'));
            }

            $arguments = [
                'id' => $stripeSubscription->getSubscriptionId(),
                'options' => null,
            ];
            $response = $apiManager->callStripeApi('\Stripe\Subscription', 'retrieve', $arguments);

            if (!$response->isSuccess()) {
                $this->addFlash(
                    'sonata_flash_error',
                    'An occurred error while retrieving subscription'
                );
                return $this->redirect($this->get('request')->headers->get('referer'));
            }

            /** @var \Stripe\Subscription $subscription */
            $subscription = $response->getContent();
            $response = $apiManager->callStripeObject($subscription, 'cancel');

            if (!$response->isSuccess()) {
                $this->addFlash(
                    'sonata_flash_error',
                    'An occurred error while cancel subscription'
                );
                return $this->redirect($this->get('request')->headers->get('referer'));
            }

            $em->remove($stripeCustomer);
        }

        /** @var Property $property */
        foreach ($user->getProperties() as $property) {
            $invitedUsers = $property->getInvitedUsers();
            foreach ($invitedUsers as $invitedUser) {
                $em->remove($invitedUser);
                $em->flush();
            }
            $em->persist($property);
        }

        $em->flush();

        $this->addFlash(
            'sonata_flash_success',
            'Success'
        );

        return $this->redirect($this->get('request')->headers->get('referer'));
    }

    /**
     * Disable Admin
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function disableAdminAction()
    {
        /** @var $user \Erp\UserBundle\Entity\User */
        $user = $this->admin->getSubject();
        if (!$user || $user->getStatus() !== User::STATUS_ACTIVE) {
            throw $this->createNotFoundException();
        }

        $this->get('erp.users.user.service')->deactivateUser($user);

        $user->setStatus(User::STATUS_DISABLED)
            ->setEnabled(false);
        $em = $this->getDoctrine()->getEntityManager();
        $em->persist($user);

        $em->flush();

        return $this->redirect($this->generateUrl('admin_erpuserbundle_administrators_list'));
    }

    /**
     * Reject Manager
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function rejectManagerAction()
    {
        /** @var $user \Erp\UserBundle\Entity\User */
        $user = $this->admin->getSubject();
        if (!$user || $user->getStatus() == User::STATUS_ACTIVE) {
            throw $this->createNotFoundException();
        }

        $this->get('erp.users.user.service')->deactivateUser($user);

        $user->setStatus(User::STATUS_REJECTED)
            ->setEnabled(false);
        $em = $this->getDoctrine()->getEntityManager();
        $em->persist($user);
        $em->flush();

        return $this->redirect($this->get('request')->headers->get('referer'));
    }

    /**
     * Removing tenant from property
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function removeTenantAction()
    {
        /** @var $property \Erp\PropertyBundle\Entity\Property */
        $property = $this->admin->getSubject();
        $tenant = $property->getTenantUser();
        if (!$tenant or $tenant->getStatus() == User::STATUS_DISABLED) {
            throw $this->createNotFoundException();
        }

        $userService = $this->get('erp.users.user.service');
        $userService->deactivateUser($tenant);
        $userService->setStatusUnreadMessages($tenant);

        $property->setStatus(Property::STATUS_DRAFT)->setTenantUser(null);
        $em = $this->getDoctrine()->getEntityManager();
        $em->persist($property);
        $em->flush();

        $this->addFlash(
            'sonata_flash_success',
            'Tenant was removed successfully and all future tenant payments were cancelled'
        );

        return $this->redirect($this->generateUrl('admin_erpuserbundle_properties_list'));
    }

    /**
     * Add manager to report
     *
     * @param ProRequest $proRequest
     * @param \DateTime  $date
     *
     * @return $this
     */
    protected function addToReport(ProRequest $proRequest, $date)
    {
        $month = $date->format('F');
        $em = $this->getDoctrine()->getEntityManager();
        $report = $em->getRepository('ErpUserBundle:ProReport')->getByConsultantAndMonth(
            $proRequest->getProConsultant(),
            $month
        );

        if ($report) {
            $report->setCountUsers($report->getCountUsers() + 1);
        } else {
            $report = new ProReport();
            $report->setProConsultant($proRequest->getProConsultant())
                ->setApprovedDate($date)
                ->setCountUsers(1);
        }

        $em->persist($report);
        $em->flush();

        return $this;
    }

    /**
     * Delete User
     *
     * @return $this
     */
    protected function deleteUser()
    {
        /** @var $user \Erp\UserBundle\Entity\User */
        $user = $this->admin->getSubject();

        if (!$user || !in_array($user->getStatus(), [User::STATUS_REJECTED, User::STATUS_DISABLED])) {
            throw $this->createNotFoundException();
        }

        $em = $this->getDoctrine()->getEntityManager();
        $em->remove($user);
        $em->flush();

        $this->addFlash('sonata_flash_ps_success', 'User was successfully deleted');

        return $this;
    }
}
