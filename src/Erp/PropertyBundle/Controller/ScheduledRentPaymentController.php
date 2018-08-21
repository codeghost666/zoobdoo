<?php

namespace Erp\PropertyBundle\Controller;

use Erp\PaymentBundle\Entity\StripeCustomer;
use Erp\PropertyBundle\Entity\ScheduledRentPayment;
use Erp\PropertyBundle\Form\Type\StopAutoWithdrawalFormType;
use Erp\UserBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class ScheduledRentPaymentController extends Controller {

    /**
     * @Security("is_granted('ROLE_TENANT')")
     */
    public function payRentAction(Request $request) {
        /** @var User $user */
        $user = $this->getUser();
        $manager = $user->getTenantProperty()->getUser();
        $managerStripeAccount = $manager->getStripeAccount();
        $tenantStripeCustomer = $user->getStripeCustomer();

        $entity = new ScheduledRentPayment();
        $entity->setProperty($user->getTenantProperty());
        $entity->setUser($user);

        $form = $this->createForm('erp_property_scheduled_rent_payment', $entity);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if (!$managerStripeAccount || !$tenantStripeCustomer) {
                $this->addFlash(
                        'alert_error', 'Please, add your payment info or contact your manager.'
                );

                return $this->redirectToRoute('erp_user_profile_dashboard');
            }

            if ($form->isValid()) {
                $startPaymentAt = $entity->getStartPaymentAt();
                $entity
                        ->setNextPaymentAt($startPaymentAt)
                        ->setAccount($managerStripeAccount)
                        ->setCustomer($tenantStripeCustomer);

                $em = $this->getDoctrine()->getManagerForClass(ScheduledRentPayment::class);
                $em->persist($entity);
                $em->flush();

                $this->addFlash(
                        'alert_ok', 'Success'
                );

                return $this->redirectToRoute('erp_user_profile_dashboard');
            }

            $this->addFlash(
                    'alert_error', $form->getErrors(true)[0]->getMessage()
            );

            return $this->redirectToRoute('erp_user_profile_dashboard');
        }

        return $this->render('ErpPaymentBundle:Stripe\Widgets:rental-payment.html.twig', [
                    'form' => $form->createView(),
                    'user' => $user,
                    'manager' => $manager,
        ]);
    }

    public function stopAutoWithdrawalAction(User $user, Request $request) {
        $stripeCustomer = $user->getStripeCustomer();

        if (!$stripeCustomer) {
            throw $this->createNotFoundException();
        }

        /** @var User $currentUser */
        $currentUser = $this->getUser();

        if ($currentUser->hasTenant($user)) {
            throw $this->createAccessDeniedException();
        }

        $entity = new ScheduledRentPayment();
        $form = $this->createForm(new StopAutoWithdrawalFormType(), $entity, ['validation_groups' => 'StopAuthWithdrawal']);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $endAt = $entity->getEndAt();
            $scheduledRentPayments = $stripeCustomer->getScheduledRentPayments();
            /** @var ScheduledRentPayment $scheduledRentPayment */
            foreach ($scheduledRentPayments as $scheduledRentPayment) {
                $scheduledRentPayment->setEndAt($endAt);
            }

            $em = $this->getDoctrine()->getManagerForClass(StripeCustomer::class);
            $em->persist($stripeCustomer);
            $em->flush();

            $this->addFlash(
                    'alert_ok', 'Success'
            );
        }

        return $this->redirect($request->headers->get('referer'));
    }

}
