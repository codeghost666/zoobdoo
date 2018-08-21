<?php

namespace Erp\UserBundle\Controller;

use Erp\CoreBundle\Controller\BaseController;
use Erp\CoreBundle\Entity\EmailNotification;
use Erp\CoreBundle\Event\EmailNotificationEvent;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Erp\UserBundle\Entity\User;
use Erp\UserBundle\Entity\ServiceRequest;
use Erp\UserBundle\Form\Type\DashboardServiceRequestFormType;

/**
 * Class ServiceRequestController
 *
 * @package Erp\UserBundle\Controller
 */
class ServiceRequestController extends BaseController
{
    /**
     * Page message
     *
     * @param int $toUserId
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function indexAction($toUserId)
    {
        /** @var User $user */
        $user = $this->getUser();

        $tenants = $user->getTenants();

        if ($toUserId === null && count($tenants)) {
            return $this->redirectToRoute(
                'erp_user_service_request',
                [
                    'toUserId' => $tenants[0]->getId()
                ]
            );
        }

        /** @var User $toUser */
        $toUser = $this->em->getRepository('ErpUserBundle:User')->findOneBy(['id' => $toUserId]);

        if (!$toUserId) {
            $renderParams = ['user' => $user];
        } elseif (!$toUser
            || ($user->hasRole(User::ROLE_MANAGER) && !$user->isTenant($toUser))
        ) {
            throw $this->createNotFoundException();
        } else {
            $serviceRequests = $this->getServiceRequests($user, $toUser);
            $serviceRequestTypes = $this->get('erp.users.user.service')
                ->getServiceRequestTypes();

            foreach ($tenants as $key => $tenant) {
                $count =
                    count($this->em->getRepository('ErpUserBundle:ServiceRequest')->findBy(['fromUser' => $tenant]));
                $tenants[$key]->setTotalServiceRequests($count);
            }

            $renderParams = [
                'user' => $user,
                'currentTenant' => $toUser,
                'tenants' => $tenants,
                'serviceRequests' => $serviceRequests,
                'serviceRequestTypes' => $serviceRequestTypes,
            ];
        }

        return $this->render('ErpUserBundle:ServiceRequests:service-requests.html.twig', $renderParams);
    }

    /**
     * Submit data from tenant to manager
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function submitAction(Request $request)
    {
        /** @var User $user - Tenant */
        $user = $this->getUser();
        /** @var User $toUser - Manager */
        $toUser = $user->getTenantProperty()->getUser();

        $serviceRequest = new ServiceRequest();
        $serviceRequestTypes = $this->get('erp.users.user.service')->getServiceRequestTypes();

        $form = $this->createForm(new DashboardServiceRequestFormType($serviceRequestTypes), $serviceRequest);
        $form->handleRequest($request);

        $serviceRequest->setFromUser($user);
        $serviceRequest->setToUser($toUser);

        $teml = 'Service Request: ';

        if ($form->isValid()) {
            $currentDate = new \DateTime();
            $selectedDateString = $form->get('date')->getData();
            $selectedDate = new \DateTime($selectedDateString);
            $selectedDate->add(new \DateInterval('PT23H59M59S'));

            if ($selectedDate >= $currentDate) {
                $serviceRequest->setDate($selectedDate);
                $this->em->persist($serviceRequest);
                $this->em->flush();

                $event = new EmailNotificationEvent(
                    $toUser,
                    EmailNotification::SETTING_SERVICE_REQUESTS,
                    [
                        '#url#' => $this->generateUrl('erp_user_service_request', ['toUserId' => $user->getId()], true)
                    ]
                );

                /** @var $dispatcher \Symfony\Component\EventDispatcher\EventDispatcherInterface */
                $dispatcher = $this->get('event_dispatcher');
                $dispatcher->dispatch(EmailNotification::EVENT_SEND_EMAIL_NOTIFICATION, $event);

                $this->get('session')->getFlashBag()->add(
                    'alert_ok',
                    $teml . 'has been successfully sent'
                );
            } else {
                $this->get('session')->getFlashBag()->add(
                    'alert_error',
                    $teml . 'incorrect date'
                );
            }
        } else {
            $errors = array();

            foreach ($form->all() as $child) {
                foreach ($child->getErrors() as $error) {
                    $errors[] = $error->getMessageTemplate();
                }
            }

            foreach ($errors as $error) {
                $this->get('session')->getFlashBag()->add('alert_error', $teml . $error);
            }
        }

        return $this->redirectToRoute('erp_user_profile_dashboard');
    }

    /**
     * Return list service requests
     *
     * @param User $user
     * @param User $toUser
     *
     * @return array
     */
    public function getServiceRequests(User $user, User $toUser)
    {
        $serviceRequests = $this->em->getRepository('ErpUserBundle:ServiceRequest')->getServiceRequests($user, $toUser);

        return $serviceRequests;
    }

    /**
     * Return list tenants by manager
     *
     * @param User $user
     *
     * @return array
     */
    protected function getTenantsByManager(User $user)
    {
        $tenants = $this->em->getRepository('ErpUserBundle:ServiceRequest')->getTenantsByManager($user);

        return $tenants;
    }
}
