<?php

namespace Erp\PropertyBundle\Controller;

use Erp\StripeBundle\Entity\Transaction;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Erp\PaymentBundle\Entity\StripeCustomer;
use Erp\PaymentBundle\Entity\StripeSubscription;
use Erp\PropertyBundle\Entity\Property;
use Erp\PropertyBundle\Entity\PropertySettings;
use Erp\PropertyBundle\Entity\Unit;
use Erp\PropertyBundle\Form\Type\UnitType;
use Erp\StripeBundle\Helper\ApiHelper;
use Erp\UserBundle\Entity\User;
use Stripe\Subscription;
use Symfony\Component\HttpFoundation\Request;

class UnitController extends Controller {

    public function buyAction(Request $request) {
        $form = $this->createForm(new UnitType());
        /** @var User $user */
        $user = $this->getUser();

        //TODO Store in DB
        $settings = [
            [
                'min' => 1,
                'max' => 1,
                'amount' => 99,
            ],
            [
                'min' => 2,
                'max' => 29,
                'amount' => 20,
            ],
            [
                'min' => 30,
                'max' => 100000,
                'amount' => 15,
            ],
        ];

        $existingUnitQuantity = $user->getProperties()->count();

        $unitPriceCalculator = $this->get('erp_property.calculator.unit_price_calculator');
        $amount = $unitPriceCalculator->calculate($existingUnitQuantity);

        $template = 'ErpPropertyBundle:Unit:form.html.twig';
        $templateParams = [
            'user' => $user,
            'form' => $form->createView(),
            'errors' => null,
            'current_year_price' => $amount,
            'total_price' => $amount,
            'existing_unit_count' => $existingUnitQuantity,
            'settings' => $settings,
        ];

        $form->handleRequest($request);

        if (!$form->isValid()) {
            return $this->render($template, $templateParams);
        }

        if (!$stripeCustomer = $user->getStripeCustomer()) {
            if ($form->isSubmitted()) {
                $templateParams['errors'] = 'Please, add bank account.';
            }

            return $this->render($template, $templateParams);
        }

        /** @var Unit $unit */
        $unit = $form->getData();
        $quantity = $unit->getQuantity();
        $newQuantity = $quantity + $existingUnitQuantity;
        $newAmount = $unitPriceCalculator->calculate($newQuantity);

        $apiManager = $this->get('erp_stripe.entity.api_manager');
        $em = $this->getDoctrine()->getManager();

        if (!$stripeSubscription = $stripeCustomer->getStripeSubscription()) {
            $arguments = [
                'params' => [
                    'customer' => $stripeCustomer->getCustomerId(),
                    'items' => [
                        [
                            'plan' => StripeSubscription::YEARLY_PLAN_ID,
                            'quantity' => $newAmount,
                        ],
                    ],
                    'trial_period_days' => StripeCustomer::TRIAL_PERIOD_DAYS,
                    'metadata' => [
                        'internalType' => Transaction::INTERNAL_TYPE_ANNUAL_SERVICE_FEE
                    ],
                ],
                'options' => null,
            ];
            $response = $apiManager->callStripeApi('\Stripe\Subscription', 'create', $arguments);

            if (!$response->isSuccess()) {
                $templateParams['errors'] = $response->getErrorMessage();
                return $this->render($template, $templateParams);
            }
            /** @var Subscription $subscription */
            $subscription = $response->getContent();

            $stripeSubscription = new StripeSubscription();
            $stripeSubscription->setSubscriptionId($subscription['id'])
                    ->setStripeCustomer($stripeCustomer)
                    ->setTrialPeriodStartAt(new \DateTime());

            $stripeCustomer->setStripeSubscription($stripeSubscription);

            $em->persist($stripeCustomer);
        } else {
            $arguments = [
                'id' => $stripeSubscription->getSubscriptionId(),
                'params' => [
                    'quantity' => $newAmount,
                ],
                'options' => null,
            ];
            $response = $apiManager->callStripeApi('\Stripe\Subscription', 'update', $arguments);

            if (!$response->isSuccess()) {
                $templateParams['errors'] = $response->getErrorMessage();
                return $this->render($template, $templateParams);
            }

            $amount = $newAmount - $amount;
            $arguments = [
                'params' => [
                    'amount' => ApiHelper::convertAmountToStripeFormat($amount),
                    'customer' => $stripeCustomer->getCustomerId(),
                    'currency' => StripeCustomer::DEFAULT_CURRENCY,
                ],
                'options' => null,
            ];
            $response = $apiManager->callStripeApi('\Stripe\Charge', 'create', $arguments);

            if (!$response->isSuccess()) {
                $templateParams['errors'] = $response->getErrorMessage();
                return $this->render($template, $templateParams);
            }
        }

        $this->addProperties($user, $quantity);

        return $this->redirectToRoute('erp_property_listings_all');
    }

    private function addProperties(User $user, $quantity) {
        $em = $this->getDoctrine()->getManagerForClass(Property::class);
        $prototype = new Property();
        for ($i = 1; $i <= $quantity; $i++) {
            $property = clone $prototype;
            $property->setUser($user);

            $property->setSettings(new PropertySettings());

            $em->persist($property);
        }

        $em->flush();
    }

}
