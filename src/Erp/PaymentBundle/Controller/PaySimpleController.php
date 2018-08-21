<?php

namespace Erp\PaymentBundle\Controller;

use Erp\CoreBundle\Controller\BaseController;
use Erp\PaymentBundle\Entity\PaySimpleCustomer;
use Erp\PaymentBundle\Entity\PaySimpleRecurringPayment;
use Erp\PaymentBundle\Form\Type\PaySimpleBankAccountFormType;
use Erp\PaymentBundle\Form\Type\PaySimpleCreditCardFormType;
use Erp\PaymentBundle\PaySimple\Managers\PaySimpleManagerFactory;
use Erp\PaymentBundle\PaySimple\Managers\PaySimpleManagerInterface;
use Erp\PaymentBundle\PaySimple\Models\PaySimpleModelInterface;
use Erp\PaymentBundle\PaySimple\Models\PaySimpleModels\BankAccountModel;
use Erp\PaymentBundle\PaySimple\Models\PaySimpleModels\CreditCardModel;
use Erp\PaymentBundle\PaySimple\Models\PaySimpleModels\RecurringPaymentModel;
use Erp\UserBundle\Entity\User;
use Symfony\Component\DependencyInjection\Exception\ParameterNotFoundException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class PaySimpleController extends BaseController
{
    /**
     * @param Request $request
     * @param string  $type
     *
     * @return Response
     */
    public function createPaymentAccountAction(Request $request, $type)
    {
        /** @var $user \Erp\UserBundle\Entity\User */
        $user = $this->getUser();
        $access = $this->checkAccessToPaymentPage($user);
        if ($access) {
            throw $access;
        }

        $errors = null;
        $form = $this->createPaySimplePaymentAccountForm($user, $type);
        $amount = $this->get('erp.core.fee.service')->getFees();
        $amount = $amount ? $amount->getErentPay() : 0;
        if ($request->getMethod() === 'POST') {
            $form->handleRequest($request);
            if ($form->isValid()) {
                /** @var $model PaySimpleModelInterface */
                $model = $form->getData();

                // Sent bank account data to admin
                if ($user->hasRole(User::ROLE_MANAGER) && $model instanceof BankAccountModel) {
                    $this->get('erp.payment.paysimple_service')->sendBankAccountToEmail($model, $user);
                }

                $errors = $this->createPaySimpleAccount($model, $user, $amount, $type);

                if (!$errors) {
                    $msg = 'Bank & Cards Information has been successfully changed';
                    if ($user->getStatus() != User::STATUS_ACTIVE) {
                        $msg = 'Please wait for account activation by Administrator';
                    }

                    $this->get('session')->getFlashBag()->add('alert_ok', $msg);

                    return $this->redirectToRoute('erp_user_profile_dashboard');
                }
            }
        }

        return $this->render(
            'ErpPaymentBundle:PaySimple:payment-form.html.twig',
            [
                'user'       => $user,
                'form'       => $form->createView(),
                'type'       => $type,
                'errors'     => $errors,
                'amount'     => $amount,
                'psCustomer' => $user->getPaySimpleCustomers()->first(),
                'isManager' => $user->hasRole(User::ROLE_MANAGER),
                'checkPaymentAmount' => $this->get('erp.core.fee.service')->getCheckPaymentFee(),
            ]
        );
    }

    /**
     * Change payment primary type
     *
     * @return RedirectResponse
     */
    public function changePaymentPrimaryTypeAction()
    {
        /** @var \Erp\PaymentBundle\Entity\PaySimpleCustomer $psCustomer */
        $psCustomer = $this->getUser() ? $this->getUser()->getPaySimpleCustomers()->first() : null;
        if (!$psCustomer) {
            throw new AccessDeniedException;
        }

        $type = $this->getChangedCustomerPrimatyTypeType($psCustomer);
        $amount = $this->get('erp.core.fee.service')->getErentpayFee();
        $recurringPayment = null;
        $psCustomer->setPrimaryType($type);

        $errors = null;
        $isHasReccuring = (bool)$psCustomer->getPsRecurringPayments()->first();

        if ($isHasReccuring or $this->getUser()->hasRole(User::ROLE_MANAGER)) {
            $recurringPayment = $this->createPaySimplePaymentRecurring($psCustomer, $amount);
            if (!$recurringPayment instanceof PaySimpleRecurringPayment) {
                $errors = $this->getErrorsFromPSResponse($recurringPayment);
                $type = $this->getChangedCustomerPrimatyTypeType($psCustomer);
                $psCustomer->setPrimaryType($type);
            }
        }
        $this->em->persist($psCustomer);
        $this->em->flush();

        $teml = 'Payment Preferences: ';
        if ($errors) {
            $this->get('session')->getFlashBag()->add('alert_error', $teml . $errors);
        } else {
            $this->get('session')->getFlashBag()->add(
                'alert_ok',
                $teml . 'has been successfully changed'
            );
        }

        return $this->redirectToRoute('erp_user_profile_dashboard');
    }

    /**
     * Get customer payment primary type
     *
     * @param PaySimpleCustomer $customer
     *
     * @return string
     */
    protected function getChangedCustomerPrimatyTypeType(PaySimpleCustomer $customer)
    {
        $type = PaySimpleManagerInterface::BANK_ACCOUNT;
        if ($customer->getPrimaryType() === PaySimpleManagerInterface::BANK_ACCOUNT) {
            $type = PaySimpleManagerInterface::CREDIT_CARD;
        }

        return $type;
    }

    /**
     * Check if user has access to payment page
     *
     * @param User $user
     *
     * @return null|AccessDeniedException
     */
    protected function checkAccessToPaymentPage(User $user = null)
    {
        $result = null;
        if (!$user || (!$user->hasRole(User::ROLE_MANAGER) && !$user->hasRole(User::ROLE_TENANT))) {
            $result = new AccessDeniedException;
        }

        return $result;
    }

    /**
     * Create all PaySimple account detatils
     *
     * @param PaySimpleModelInterface $model
     * @param User                    $user
     * @param float                   $amount
     * @param string                  $type
     *
     * @return mixed
     */
    private function createPaySimpleAccount(PaySimpleModelInterface $model, User $user, $amount, $type)
    {
        $errors = null;

        $psCustomer = $this->getPaySimpleCustomer($model, $user);
        if (!$psCustomer instanceof PaySimpleCustomer) {
            $errors = $this->getErrorsFromPSResponse($psCustomer);
        } else {
            $model->setCustomer($psCustomer);
            $customerPaymentAcc = $this->createPaySimplePaymentAccount($model, $type);

            if (!$customerPaymentAcc instanceof PaySimpleCustomer) {
                $this->get('erp.logger')->add('paysimple', json_encode($customerPaymentAcc), 'RECURRING_ERROR');
                $errors = $this->getErrorsFromPSResponse($customerPaymentAcc);
            } else {
                $issetRecurring = $this->em->getRepository('ErpPaymentBundle:PaySimpleCustomer')
                    ->getLastActiveRecurring($psCustomer);
                $isPrimaryType = $psCustomer->getPrimaryType() == $type;
                $isLandWithoutRecurring = !$issetRecurring && $user->hasRole(User::ROLE_MANAGER);
                $isTenantWithRecurring = $user->hasRole(User::ROLE_TENANT) && $issetRecurring;
                $isManagerNeedUpdate = $user->hasRole(User::ROLE_MANAGER) && $issetRecurring && $isPrimaryType;
                $isTenantNeedUpdate = $isPrimaryType && $isTenantWithRecurring;

                if (($isLandWithoutRecurring || $isManagerNeedUpdate || $isTenantNeedUpdate)
                    && $user->getIsActiveMonthlyFee()
                ) {
                    $recurringPayment = $this->createPaySimplePaymentRecurring($psCustomer, $amount);
                    $isStatusActive = $user->getStatus() == User::STATUS_ACTIVE;

                    if (!$issetRecurring && $user->hasRole(User::ROLE_MANAGER) && !$isStatusActive) {
                        $this->em->persist($user->setStatus(User::STATUS_NOT_CONFIRMED));
                        $this->em->flush();
                    }

                    if (!$recurringPayment instanceof PaySimpleRecurringPayment) {
                        $errors = $this->getErrorsFromPSResponse($recurringPayment);
                    } else {
                        $this->get('session')->getFlashBag()->add('alert_ok', 'Payment was successful!');
                    }
                }
            }
        }

        return $errors;
    }

    /**
     * Create PaySimple payment account form
     *
     * @param User $user
     * @param string $type
     *
     * @return \Symfony\Component\Form\Form
     */
    private function createPaySimplePaymentAccountForm(User $user, $type)
    {
        /** @var $userService \Erp\UserBundle\Services\UserService */
        $userService = $this->get('erp.users.user.service');
        switch ($type) {
            case PaySimpleManagerInterface::CREDIT_CARD:
                $form = new PaySimpleCreditCardFormType($user);
                break;
            case PaySimpleManagerInterface::BANK_ACCOUNT:
                $form = new PaySimpleBankAccountFormType($user);
                break;
            default:
                throw new ParameterNotFoundException($type);
                break;
        }

        $model = $userService->getPaySimplePaymentModel($user, $type);
        $formOptions = [
            'action' => $this->generateUrl('erp_payment_ps_create_account', ['type' => $type]),
            'method' => 'POST'
        ];
        $form = $this->createForm($form, $model, $formOptions);

        return $form;
    }

    /**
     * Create Pay Simple Customer
     *
     * @param PaySimpleModelInterface $model
     * @param User                    $user
     *
     * @return array
     */
    private function getPaySimpleCustomer(PaySimpleModelInterface $model, User $user)
    {
        $psCustomer = $user->getPaySimpleCustomers()->first();
        if (!$psCustomer) {
            $user->setFirstName($model->getFirstName())
                ->setLastName($model->getLastName());

            $psCustomer = $this->get('erp.users.user.service')->createPaySimpleCustomer($user);
            $psCustomer = ($psCustomer['status'] === false) ? $psCustomer['errors'] : $psCustomer['data'];
        }

        return $psCustomer;
    }

    /**
     * Create Pay Simple Customer Payment Account
     *
     * @param PaySimpleModelInterface $model
     * @param string                  $type
     *
     * @return array
     */
    private function createPaySimplePaymentAccount(PaySimpleModelInterface $model, $type)
    {
        $customerPayAcc = $this->get('erp.users.user.service')->createPaySimplePayment($model, $type);
        $pAccount = ($customerPayAcc['status'] === false) ? $customerPayAcc['errors'] : $customerPayAcc['data'];

        return $pAccount;
    }

    /**
     * Create Pay Simple Customer Payment Recurring
     *
     * @param PaySimpleCustomer $customer
     * @param float             $amount
     *
     * @return array
     */
    private function createPaySimplePaymentRecurring(PaySimpleCustomer $customer, $amount)
    {
        $startDate = new \DateTime();

        $psRecurringPayment = $this->em->getRepository('ErpPaymentBundle:PaySimpleCustomer')
            ->getLastActiveRecurring($customer);
        if ($psRecurringPayment) {
            $amount = $psRecurringPayment->getMonthlyAmount();
            $startDate = $psRecurringPayment->getNextDate();
        }

        $model = new RecurringPaymentModel();
        $model->setCustomer($customer)
            ->setPsReccuringPayment($psRecurringPayment)
            ->setAmount($amount)
            ->setStartDate($startDate);

        $customerRecAcc = $this->get('erp.users.user.service')->createPaySimplePaymentRecurring($model);
        $this->get('erp.logger')->add('paysimple', json_encode($customerRecAcc), 'CREATE_RECURRING');

        if ($customerRecAcc['status'] === false) {
            $paySimpleCredentials = $this->get('erp.users.user.service')
                ->getCurrentPaySimpleCredentials($model->getCustomer()->getUser());

            $recurring = $customerRecAcc['errors'];
            $activeSchedules = $this->get('erp.users.user.service')->getActiveCustomerSchedules($model);
            $paySimpleManager = PaySimpleManagerFactory::getManagerInstance(
                PaySimpleManagerInterface::TYPE_RECURRING,
                $this->container,
                $paySimpleCredentials['paySimpleLogin'],
                $paySimpleCredentials['paySimpleApiSecretKey']
            );

            foreach ($activeSchedules['data'] as $activeSchedule) {
                $psRecurringPayment = new PaySimpleRecurringPayment();
                $psRecurringPayment->setRecurringId($activeSchedule['Id']);
                $model->setPsReccuringPayment($psRecurringPayment);
                $result = $paySimpleManager->setModel($model)->proccess(
                    PaySimpleManagerInterface::METHOD_RECURRING_SUSPEND
                );
                $this->get('erp.logger')->add('paysimple', json_encode($result), 'SUSPEND_SCHEDULE');
            }
        } else {
            $recurring = $customerRecAcc['data'];
        }

        return $recurring;
    }

    /**
     * Get errors from Pay Simple response
     *
     * @param array $object
     *
     * @return string
     */
    private function getErrorsFromPSResponse($object)
    {
        $errors = 'An error has occurred. Please, try latter';
        if (is_array($object)) {
            if (array_key_exists('errors', $object)) {
                $errors = $object['errors'];
            } elseif (isset($object[0]['Message'])) {
                $errors = $object[0]['Message'];
            }
        }

        return $errors;
    }
}
