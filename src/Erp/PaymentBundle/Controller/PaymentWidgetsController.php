<?php

namespace Erp\PaymentBundle\Controller;

use Erp\CoreBundle\Controller\BaseController;
use Erp\PaymentBundle\Entity\PaySimpleHistory;
use Erp\PaymentBundle\Entity\PaySimpleRecurringPayment;
use Erp\PaymentBundle\Form\Type\PaySimpleHistoryExportFormType;
use Erp\PaymentBundle\Form\Type\PaySimplePayRentFormType;
use Erp\PaymentBundle\PaySimple\Managers\PaySimpleManagerFactory;
use Erp\PaymentBundle\PaySimple\Managers\PaySimpleManagerInterface;
use Erp\PaymentBundle\PaySimple\Models\PaySimpleModels\RecurringPaymentModel;
use Erp\PaymentBundle\PaySimple\Models\PaySimpleModels\RentPaymentModel;
use Erp\PropertyBundle\Entity\Property;
use Erp\UserBundle\Entity\User;
use Goodby\CSV\Export\Standard\CsvFileObject;
use Goodby\CSV\Export\Standard\Exporter;
use Goodby\CSV\Export\Standard\ExporterConfig;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PaymentWidgetsController extends BaseController
{
    /**
     * Pay Rent form widget
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function payRentAction(Request $request)
    {
        /** @var $user \Erp\UserBundle\Entity\User */
        $user = $this->getUser();
        $tenantProperty = $user->getTenantProperty();
        $isUserProperty = false;
        if ($tenantProperty && $tenantProperty->getTenantUser() == $user) {
            $isUserProperty = true;
        }

        if (!$isUserProperty || !$user->hasRole(User::ROLE_TENANT)) {
            throw $this->createNotFoundException();
        }

        $form = $this->createFormPayRent($tenantProperty);
        if ($request->getMethod() === 'POST') {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $rentPayment = $form->getData();

                $result = $this->makeRentPayment($rentPayment);
                if (!$result['status']) {
                    $this->get('session')->getFlashBag()->add(
                        'alert_error',
                        $this->get('erp.users.user.service')->getPaySimpleErrorByCode('error')
                    );
                } else {
                    $msg = ($rentPayment->getIsRecurring())
                        ? $this->get('erp.users.user.service')->getPaySimpleErrorByCode('pay_rent_recurring_ok')
                        : $this->get('erp.users.user.service')->getPaySimpleErrorByCode('pay_rent_tenant_ok');

                    $this->get('session')->getFlashBag()->add(
                        'alert_ok',
                        $msg
                    );
                }
            } else {
                $this->addPayRentFormErrors($form);
            }

            return $this->redirectToRoute('erp_user_profile_dashboard');
        }

        /** @var $userService \Erp\UserBundle\Services\UserService */
        $userService = $this->get('erp.users.user.service');
        $userCCInfo = $userService->getCustomerPaymentAccountInfo(
            $user,
            PaySimpleManagerInterface::METHOD_PAYMENT_GET_DEFAULT_CC
        );
        $userBAInfo = $userService->getCustomerPaymentAccountInfo(
            $user,
            PaySimpleManagerInterface::METHOD_PAYMENT_GET_DEFAULT_BA
        );

        return $this->render(
            'ErpPaymentBundle:PaySimple/Widgets:pay-rent.html.twig',
            [
                'form'           => $form->createView(),
                'userCCInfo'     => $userCCInfo,
                'userBAInfo'     => $userBAInfo,
                'psCustomer'     => $user->getPaySimpleCustomers()->first(),
                'tenantProperty' => $tenantProperty
            ]
        );
    }

    /**
     * Bank & Cards Information widget
     *
     * @return RedirectResponse
     */
    public function bankCardsAction()
    {
        /** @var $user \Erp\UserBundle\Entity\User */
        $user = $this->getUser();
        if (!$user->hasRole(User::ROLE_TENANT) && !$user->hasRole(User::ROLE_MANAGER)) {
            throw $this->createNotFoundException();
        }

        /** @var $userService \Erp\UserBundle\Services\UserService */
        $userService = $this->get('erp.users.user.service');
        $userCCInfo = $userService->getCustomerPaymentAccountInfo(
            $user,
            PaySimpleManagerInterface::METHOD_PAYMENT_GET_DEFAULT_CC
        );
        $userBAInfo = $userService->getCustomerPaymentAccountInfo(
            $user,
            PaySimpleManagerInterface::METHOD_PAYMENT_GET_DEFAULT_BA
        );

        $forTenant = false;
        if ($user->hasRole(User::ROLE_TENANT)) {
            $forTenant = true;
        }

        return $this->render(
            'ErpPaymentBundle:PaySimple/Widgets:bank-cards-info.html.twig',
            [
                'userCCInfo' => $userCCInfo,
                'userBAInfo' => $userBAInfo,
                'psCustomer' => $user->getPaySimpleCustomers()->first(),
                'forTenant'  => $forTenant,
            ]
        );
    }

    /**
     * Payment Preferences widget
     *
     * @return RedirectResponse
     */
    public function paymentsPreferencesAction()
    {
        /** @var $user \Erp\UserBundle\Entity\User */
        $user = $this->getUser();
        if (!$user->hasRole(User::ROLE_TENANT) && !$user->hasRole(User::ROLE_MANAGER)) {
            throw $this->createNotFoundException();
        }

        return $this->render(
            'ErpPaymentBundle:PaySimple/Widgets:payments-preferences.html.twig',
            [
                'psCustomer' => $user->getPaySimpleCustomers()->first(),
                'isTenant' => $user->hasRole(User::ROLE_TENANT),
                'ccTransactionFeePercent' => $this->get('erp.core.fee.service')->getCcTransactionFee(),
                'checkPaymentAmount' => $this->get('erp.core.fee.service')->getCheckPaymentFee(),
            ]
        );
    }

    /**
     * History widget
     *
     * @param Request $request
     *
     * @return Response
     */
    public function paymentHistoryAction(Request $request)
    {
        /** @var $user \Erp\UserBundle\Entity\User */
        $user = $this->getUser();
        if (!$user or !$user->hasRole(User::ROLE_MANAGER)) {
            throw $this->createNotFoundException();
        }

        $formAttr = ['action' => $this->generateUrl('erp_payment_ps_history'), 'method' => 'POST'];
        $form = $this->createForm(new PaySimpleHistoryExportFormType(), null, $formAttr);

        $psHistory = $this->em->getRepository('ErpPaymentBundle:PaySimpleHistory')->getManagerHistory($user);
        $response = $this->render(
            'ErpPaymentBundle:PaySimple/Widgets:payments-history.html.twig',
            [
                'user' => $user,
                'psHistories' => $psHistory,
                'form' => $form->createView()
            ]
        );

        if ($request->getMethod() === 'POST') {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $startDate = $form->getData()->getStartDate();
                $endDate = $form->getData()->getEndDate();

                if ($form->get('pdf_submit')->isClicked()) {
                    $response = $this->getPDFResponse($user, $startDate, $endDate);
                } elseif ($form->get('csv_submit')->isClicked()) {
                    $response = $this->getCSVResponse($user, $startDate, $endDate);
                }
            } else {
                $this->addHistoryFormErrors($form);

                $response = $this->redirectToRoute('erp_user_profile_dashboard');
            }
        }

        return $response;
    }

    /**
     * Export PaySimple History to pdf
     *
     * @param User      $user
     * @param \DateTime $startDate
     * @param \DateTime $endDate
     *
     * @return StreamedResponse
     */
    private function getPDFResponse(User $user, $startDate, $endDate)
    {
        $psHistory = $this->em->getRepository('ErpPaymentBundle:PaySimpleHistory')->getManagerHistory(
            $user,
            $startDate,
            $endDate
        );
        $html = $this->renderView(
            'ErpPaymentBundle:PaySimple:export-history.html.twig',
            ['psHistories' => $psHistory]
        );
        /** @var $dompdf \Slik\DompdfBundle\Wrapper\DompdfWrapper */
        $dompdf = $this->get('slik_dompdf');
        $dompdf->getpdf($html);
        $dompdf->stream('Payment_History.pdf', ['Attachment' => '0']);
        $response = $dompdf->output();

        return $response;
    }

    /**
     * Export PaySimple History to csv
     *
     * @param User      $user
     * @param \DateTime $startDate
     * @param \DateTime $endDate
     *
     * @return StreamedResponse
     */
    private function getCSVResponse(User $user, $startDate, $endDate)
    {
        $psHistory = $this->em->getRepository('ErpPaymentBundle:PaySimpleHistory')->getManagerHistory(
            $user,
            $startDate,
            $endDate
        );

        $csvHistory = [];
        $i = 1;
        /** @var $history \Erp\PaymentBundle\Entity\PaySimpleHistory */
        foreach ($psHistory as $history) {
            $payDate = $history->getPaymentDate();
            $noteText = 'Payment Success';
            if ($history->getStatus() != PaySimpleHistory::STATUS_SUCCESS) {
                $noteText = 'Payment Error';
            }
            $csvHistory[] = [
                $i,
                $payDate->format('m/d/Y'),
                $history->getProperty()->getFullAddress(),
                $history->getAmount(),
                $noteText
            ];
            $i++;
        }

        $response = new StreamedResponse();
        $response->headers->set('Content-Type', 'text/csv; charset=utf-8');
        $response->headers->set('Content-Disposition', 'attachment; filename="Payment_History.csv"');
        $response->setCallback(
            function () use ($csvHistory) {
                $config = new ExporterConfig();
                $config->setDelimiter(';')->setToCharset('UTF-8')->setFileMode(CsvFileObject::FILE_MODE_WRITE);
                $exporter = new Exporter($config);
                $exporter->export('php://output', $csvHistory);
            }
        );
        $response->setStatusCode(Response::HTTP_OK)->send();

        return $response;
    }

    /**
     * Add Errors to Pay Rent form widget
     *
     * @param Form $form
     *
     * @return $this
     */
    private function addPayRentFormErrors(Form $form)
    {
        $teml = 'Pay Rent: ';
        $amountErr = $form->get('amount')->getErrors();
        if (isset($amountErr[0])) {
            $this->get('session')->getFlashBag()->add('alert_error', $teml . $amountErr[0]->getMessageTemplate());
        }
        $startDateErr = $form->get('startDate')->getErrors();
        if (isset($startDateErr[0])) {
            $this->get('session')->getFlashBag()->add('alert_error', $teml . $startDateErr[0]->getMessageTemplate());
        }

        return $this;
    }

    /**
     * Add Errors to Pay Rent form widget
     *
     * @param Form $form
     *
     * @return $this
     */
    private function addHistoryFormErrors(Form $form)
    {
        $teml = 'Payment History: ';
        $startDateErr = $form->get('startDate')->getErrors();
        if (isset($startDateErr[0])) {
            $this->get('session')->getFlashBag()->add('alert_error', $teml . $startDateErr[0]->getMessageTemplate());
        }
        $endDateErr = $form->get('endDate')->getErrors();
        if (isset($endDateErr[0])) {
            $this->get('session')->getFlashBag()->add('alert_error', $teml . $endDateErr[0]->getMessageTemplate());
        }

        return $this;
    }

    /**
     * Create Service Request form
     *
     * @param Property $property
     *
     * @return \Symfony\Component\Form\Form
     */
    private function createFormPayRent(Property $property)
    {
        $psCustomer = $property->getTenantUser()->getPaySimpleCustomers()->first();

        $payRentModel = new RentPaymentModel();
        if ($psCustomer) {
            $payRentModel->setCustomer($psCustomer)
                ->setAmount($property->getPrice());
        }

        $formOptions = ['action' => $this->generateUrl('erp_payment_ps_pay_rent'), 'method' => 'POST'];
        $form = $this->createForm(
            new PaySimplePayRentFormType($property->getTenantUser()),
            $payRentModel,
            $formOptions
        );

        return $form;
    }

    /**
     * Make payment for tenant
     *
     * @param RentPaymentModel $rentPaymentModel
     *
     * @return mixed
     * @throws PaySimpleManagerException
     */
    private function makeRentPayment(RentPaymentModel $rentPaymentModel)
    {
        $customer = $rentPaymentModel->getCustomer();
        $amount = $rentPaymentModel->getAmount();

        $rentAllowance = $this->get('erp.core.fee.service')->getRentAllowanceAmount();

        if ($customer->getPrimaryType() === PaySimpleManagerInterface::CREDIT_CARD) {
            $rentAllowance = $amount * ($this->get('erp.core.fee.service')->getCcTransactionFee() / 100);
        }

        $accountId = $customer->getPrimaryType() === PaySimpleManagerInterface::CREDIT_CARD
            ? $customer->getCcId()
            : $customer->getBaId();

        $psRecurringPayment = null;
        if ($customer->getPsRecurringPayments()->first()) {
            $psRecurringPayment = $customer->getPsRecurringPayments()->first();
        }

        $model = new RecurringPaymentModel();
        $model->setCustomer($customer)
            ->setAmount($amount)
            ->setAllowance($rentAllowance)
            ->setStartDate($rentPaymentModel->getStartDate())
            ->setAccountId($accountId)
            ->setPsReccuringPayment($psRecurringPayment);

        if ($rentPaymentModel->getIsRecurring()) {
            $response = $this->get('erp.users.user.service')->createPaySimplePaymentRecurring($model);
        } else {
            $response = $this->get('erp.users.user.service')->makeOnePayment($model);
        }

        return $response;
    }
}
