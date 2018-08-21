<?php

namespace Erp\PaymentBundle\Controller;

use Erp\CoreBundle\Controller\BaseController;
use Erp\PaymentBundle\Entity\StripeAccount;
use Erp\PaymentBundle\Entity\StripeCustomer;
use Erp\PaymentBundle\Form\Type\StripeCreditCardType;
use Erp\PaymentBundle\Plaid\Exception\ServiceException;
use Erp\PaymentBundle\Stripe\Model\CreditCard;
use Erp\UserBundle\Entity\User;
use Erp\StripeBundle\Form\Type\AccountVerificationType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Stripe\Account;
use Stripe\Customer;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class StripeController extends BaseController {

    //TODO Optimize logic
    public function saveCreditCardAction(Request $request) {
        $model = new CreditCard();
        $form = $this->createForm(new StripeCreditCardType(), $model);
        $form->handleRequest($request);
        /** @var $user User */
        $user = $this->getUser();

        $template = 'ErpPaymentBundle:Stripe/Forms:cc.html.twig';
        $templateParams = [
            'user' => $user,
            'form' => $form->createView(),
            'errors' => null,
            'customer' => $user->getStripeCustomer(),
        ];

        if ($form->isValid()) {
            $manager = $user->getTenantProperty()->getUser();
            $managerStripeAccountId = $manager->getStripeAccount()->getAccountId();

            $stripeToken = $model->getToken();
            $options = ['stripe_account' => $managerStripeAccountId];

            $stripeCustomer = $user->getStripeCustomer();
            $customerManager = $this->get('erp.payment.stripe.manager.customer_manager');

            if (!$stripeCustomer) {
                $response = $customerManager->create(
                        [
                    'email' => $user->getEmail(),
                    'source' => $stripeToken,
                        ], $options
                );

                if (!$response->isSuccess()) {
                    $templateParams['errors'] = $response->getErrorMessage();
                    return $this->render($template, $templateParams);
                }
                /** @var Customer $customer */
                $customer = $response->getContent();

                $stripeCustomer = new StripeCustomer();
                $stripeCustomer->setCustomerId($customer['id'])
                        ->setUser($user);

                $em = $this->getDoctrine()->getManagerForClass(StripeCustomer::class);
                $em->persist($stripeCustomer);
                $em->flush();
            } else {
                $response = $customerManager->retrieve($stripeCustomer->getCustomerId(), $options);

                if (!$response->isSuccess()) {
                    $templateParams['errors'] = $response->getErrorMessage();
                    return $this->render($template, $templateParams);
                }

                /** @var Customer $customer */
                $customer = $response->getContent();
                $response = $customerManager->update(
                        $customer, [
                    'source' => $stripeToken,
                        ], $options
                );

                if (!$response->isSuccess()) {
                    $templateParams['errors'] = $response->getErrorMessage();
                    return $this->render($template, $templateParams);
                }
            }

            return $this->redirectToRoute('erp_user_profile_dashboard');
        }

        return $this->render($template, $templateParams);
    }

    //TODO Optimize logic
    public function verifyBankAccountAction(Request $request) {
        $publicToken = $request->get('publicToken');
        $accountId = $request->get('accountId');

        try {
            $stripeBankAccountToken = $this->createBankAccountToken($publicToken, $accountId);
        } catch (ServiceException $e) {
            $this->addFlash(
                    'alert_error', $e->getMessage()
            );

            return $this->redirect($this->generateUrl('erp_user_dashboard_dashboard'));
        }

        $apiManager = $this->get('erp_stripe.entity.api_manager');
        /** @var $user User */
        $user = $this->getUser();
        $stripeCustomer = $user->getStripeCustomer();

        $options = null;
        if ($user->hasRole(User::ROLE_TENANT)) {
            $manager = $user->getTenantProperty()->getUser();
            $managerStripeAccountId = $manager->getStripeAccount()->getAccountId();
            $options = ['stripe_account' => $managerStripeAccountId];
        }

        if (!$stripeCustomer) {
            $arguments = [
                'params' => [
                    'email' => $user->getEmail(),
                    'source' => $stripeBankAccountToken,
                ],
                'options' => $options,
            ];
            $response = $apiManager->callStripeApi('\Stripe\Customer', 'create', $arguments);

            if (!$response->isSuccess()) {
                return new JsonResponse(
                        [
                    'success' => false,
                    'error' => $response->getErrorMessage(),
                        ]
                );
            }
            /** @var Customer $customer */
            $customer = $response->getContent();

            $stripeCustomer = new StripeCustomer();
            $stripeCustomer->setCustomerId($customer['id'])
                    ->setUser($user);

            $em = $this->getDoctrine()->getManagerForClass(StripeCustomer::class);
            $em->persist($stripeCustomer);
            $em->flush();
        } else {
            $arguments = [
                'id' => $stripeCustomer->getCustomerId(),
                'params' => ['source' => $stripeBankAccountToken],
                'options' => $options,
            ];
            $response = $apiManager->callStripeApi('\Stripe\Customer', 'update', $arguments);

            if (!$response->isSuccess()) {
                $this->addFlash(
                        'alert_error', $response->getErrorMessage()
                );

                return $this->redirect($this->generateUrl('erp_user_dashboard_dashboard'));
            }
        }

        if ($user->hasRole(User::ROLE_MANAGER)) {
            try {
                $stripeBankAccountToken = $this->createBankAccountToken($publicToken, $accountId);
            } catch (ServiceException $e) {
                $this->addFlash(
                        'alert_error', $e->getMessage()
                );

                return $this->redirect($this->generateUrl('erp_user_dashboard_dashboard'));
            }

            $stripeAccount = $user->getStripeAccount();
            if (!$stripeAccount->getAccountId()) {
                $params = array_merge(
                        $stripeAccount->toStripe(), [
                    'country' => StripeAccount::DEFAULT_ACCOUNT_COUNTRY,
                    'type' => StripeAccount::DEFAULT_ACCOUNT_TYPE,
                    'external_account' => $stripeBankAccountToken,
                        ]
                );
                $arguments = [
                    'params' => $params,
                    'options' => null,
                ];

                $response = $apiManager->callStripeApi('\Stripe\Account', 'create', $arguments);

                if (!$response->isSuccess()) {
                    $this->addFlash(
                            'alert_error', $response->getErrorMessage()
                    );

                    return $this->redirect($this->generateUrl('erp_user_dashboard_dashboard'));
                }
                /** @var Account $account */
                $account = $response->getContent();

                $stripeAccount->setAccountId($account['id']);

                $em = $this->getDoctrine()->getManagerForClass(StripeAccount::class);
                $em->persist($stripeAccount);
                // Force flush for saving Stripe account
                $em->flush();
            } else {
                $arguments = [
                    'id' => $stripeAccount->getAccountId(),
                    'params' => ['external_account' => $stripeBankAccountToken],
                ];
                $response = $apiManager->callStripeApi('\Stripe\Account', 'update', $arguments);

                if (!$response->isSuccess()) {
                    $this->addFlash(
                            'alert_error', $response->getErrorMessage()
                    );

                    return $this->redirect($this->generateUrl('erp_user_dashboard_dashboard'));
                }

                $arguments = [
                    'id' => $stripeCustomer->getCustomerId(),
                    'params' => ['source' => $stripeBankAccountToken],
                    'options' => $options,
                ];
                $response = $apiManager->callStripeApi('\Stripe\Customer', 'update', $arguments);

                if (!$response->isSuccess()) {
                    $this->addFlash(
                            'alert_error', $response->getErrorMessage()
                    );

                    return $this->redirect($this->generateUrl('erp_user_dashboard_dashboard'));
                }
            }
        }

        if ($user->hasRole(User::ROLE_MANAGER)) {
            $url = $this->generateUrl('erp_property_unit_buy');
        } else {
            $url = $this->generateUrl('erp_user_profile_dashboard');
        }

        $this->addFlash(
                'alert_ok', 'Bank account has been verified successfully'
        );

        return $this->redirect($url);
    }

    /**
     * @Security("is_granted('ROLE_MANAGER')")
     */
    public function verifyAccountAction(Request $request) {
        //TODO Need to verify account if I change BA?
        /** @var User $user */
        $user = $this->getUser();
        $stripeAccount = $user->getStripeAccount();

        $form = $this->createForm(new AccountVerificationType(), $stripeAccount, ['validation_groups' => 'US']);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $stripeAccount->setTosAcceptanceDate(new \DateTime())
                    ->setTosAcceptanceIp($request->getClientIp());

            $apiManager = $this->get('erp_stripe.entity.api_manager');
            $arguments = [
                'id' => $stripeAccount->getAccountId(),
                'params' => $stripeAccount->toStripe(),
                'options' => null,
            ];
            $response = $apiManager->callStripeApi('\Stripe\Account', 'update', $arguments);

            if (!$response->isSuccess()) {
                return new JsonResponse([
                    'success' => false,
                    'error' => $response->getErrorMessage(),
                ]);
            }

            /** @var Account $content */
            $content = $response->getContent();
            if ($fieldsNeeded = $content->verification->fields_needed) {
                //TODO Handle Stripe required verification fields
                return new JsonResponse([
                    'success' => false,
                    'fields_needed' => $fieldsNeeded,
                ]);
            }

            $em = $this->getDoctrine()->getManagerForClass(StripeAccount::class);
            $em->persist($stripeAccount);
            $em->flush();

            if ($user->hasRole(User::ROLE_MANAGER)) {
                $url = $this->generateUrl('erp_property_unit_buy');
            } else {
                $url = $this->generateUrl('erp_user_profile_dashboard');
            }

            return new JsonResponse([
                'redirect' => $url,
            ]);
        }
        //TODO Prepare backend errors for frontend
        return $this->render('ErpStripeBundle:Widget:verification_ba.html.twig', [
                    'form' => $form->createView(),
                    'modalTitle' => 'Continue verification',
        ]);
    }

    private function createBankAccountToken($publicToken, $accountId) {
        $itemPlaidService = $this->get('erp.payment.plaid.service.item');
        $processorPlaidService = $this->get('erp.payment.plaid.service.processor');

        $response = $itemPlaidService->exchangePublicToken($publicToken);
        $result = json_decode($response['body'], true);

        if ($response['code'] < 200 || $response['code'] >= 300) {
            throw new ServiceException($result['display_message']);
        }

        $response = $processorPlaidService->createBankAccountToken($result['access_token'], $accountId);
        $result = json_decode($response['body'], true);

        if ($response['code'] < 200 || $response['code'] >= 300) {
            throw new ServiceException($result['display_message']);
        }

        return $result['stripe_bank_account_token'];
    }

}
