<?php

namespace Erp\PaymentBundle\Services;

use Erp\CoreBundle\EmailNotification\EmailNotificationFactory;
use Erp\PaymentBundle\Entity\PaySimpleCustomer;
use Erp\PaymentBundle\Entity\PaySimpleDeferredPayments;
use Erp\PaymentBundle\Entity\PaySimpleHistory;
use Erp\PaymentBundle\Entity\PaySimpleRecurringPayment;
use Erp\PaymentBundle\PaySimple\Models\PaySimpleModels\BankAccountModel;
use Erp\PaymentBundle\PaySimple\Models\PaySimpleModels\RecurringPaymentModel;
use Erp\UserBundle\Entity\User;
use Symfony\Component\DependencyInjection\ContainerInterface;

class PaySimpleService
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $em;

    /**
     * @var \Symfony\Bridge\Monolog\Logger
     */
    protected $logger;

    /**
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container = null)
    {
        $this->container = $container;
        $this->em = $this->container->get('doctrine')->getManager();
        $this->logger = $this->container->get('erp.logger');
    }

    /**
     * Check and caharge tenants
     *
     * @return $this
     */
    public function chargeTenants()
    {
        $currentDate = (new \DateTime())->setTime(0, 0);
        $needToCharge = $this->em->getRepository('ErpPaymentBundle:PaySimpleDeferredPayments')->findBy(
            ['paymentDate' => $currentDate, 'status' => PaySimpleDeferredPayments::STATUS_PENDING, 'makedDate' => null]
        );

        foreach ($needToCharge as $tenantDeferredPayment) {
            $this->logger->add('paysimple', 'PaySimpleDeferredPayments[id] = '.$tenantDeferredPayment->getId());
            $this->chargeInPaySimple($tenantDeferredPayment);
        }

        return $this;
    }

    /**
     * Check recurring
     *
     * @param PaySimpleRecurringPayment|null $neededRecurring
     *
     * @return $this
     */
    public function checkRecurring(PaySimpleRecurringPayment $neededRecurring = null)
    {
        if (!$neededRecurring) {
            $needCheckRecurring = $this->em->getRepository('ErpPaymentBundle:PaySimpleRecurringPayment')
                ->getUserRecurringForCheck();
        } else {
            $needCheckRecurring[] = $neededRecurring;
        }

        foreach ($needCheckRecurring as $psRecurring) {
            $this->logger->add('paysimple', 'PaySimpleRecurringPayment[id] = '.$psRecurring->getId());
            $this->checkInPaySimple($psRecurring);
        }

        $this->em->flush();

        return $this;
    }

    /**
     * Charge tenants in Pay Simple
     *
     * @param PaySimpleDeferredPayments $defPayment
     *
     * @return $this
     */
    protected function chargeInPaySimple(PaySimpleDeferredPayments $defPayment)
    {
        $model = new RecurringPaymentModel();
        $model->setCustomer($defPayment->getPaySimpleCustomer())
            ->setAmount($defPayment->getAmount())
            ->setAllowance($defPayment->getAllowance())
            ->setStartDate($defPayment->getPaymentDate())
            ->setAccountId($defPayment->getAccountId());

        /** @var $deferredPaymentResp PaySimpleDeferredPayments */
        $deferredPaymentResp = $this->container->get('erp.users.user.service')->makeOnePayment($model, $defPayment);
        $this->logger->add('paysimple', 'PaySimple Response => ' . $deferredPaymentResp['status']);

        $payment = false;
        if ($deferredPaymentResp['status']) {
            $payment = $deferredPaymentResp['data'];
        }

        if (!$payment || $payment->getStatus() === PaySimpleDeferredPayments::STATUS_FAILED) {
            $this->sendPaymentEmail($defPayment->getPaySimpleCustomer());
            $this->logger->add('paysimple', json_encode($payment), 'ERROR');
            $this->logger->add('paysimple', 'Errors from PaySimple:' . $deferredPaymentResp['errors'], 'ERROR');
            $this->logger->add(
                'paysimple',
                'Sending mail to userId=' . $defPayment->getPaySimpleCustomer()->getUser()->getId(),
                'ERROR'
            );
        }

        return $this;
    }

    /**
     * Check user recurring status in Pay Simple
     *
     * @param PaySimpleRecurringPayment $needCheckRecurring
     *
     * @return $this
     */
    protected function checkInPaySimple(PaySimpleRecurringPayment $needCheckRecurring)
    {
        $checkedDate = (new \DateTime())->setTime(0, 0);
        $psCustomer = $needCheckRecurring->getPaySimpleCustomer();
        $user = $psCustomer->getUser();

        $model = new RecurringPaymentModel();
        $model->setCustomer($psCustomer)
            ->setPsReccuringPayment($needCheckRecurring)
            ->setAmount($needCheckRecurring->getMonthlyAmount())
            ->setAllowance($needCheckRecurring->getAllowance())
            ->setStartDate($needCheckRecurring->getStartDate());

        $response = $this->container->get('erp.users.user.service')->retrieveRecurringPayment($model);

        $this->logger->add('paysimple', 'PaySimple Response => ' . json_encode($response));

        if ($response['status']) {
            $this->logger->add('paysimple', 'Recurring ScheduleStatus => ' . $response['data']['ScheduleStatus']);

            $needCheckRecurring->setStatus($response['data']['ScheduleStatus'])
                ->setNextDate(new \DateTime($response['data']['NextScheduleDate']));

            if ($user->hasRole(User::ROLE_MANAGER) && $user->getStatus() === User::STATUS_PENDING) {
                $user->setStatus(User::STATUS_NOT_CONFIRMED);
                $this->em->persist($user);
            } elseif ($user->hasRole(User::ROLE_TENANT)) {
                $status = $needCheckRecurring->getStatus() == PaySimpleRecurringPayment::STATUS_ACTIVE
                    ? PaySimpleHistory::STATUS_SUCCESS
                    : PaySimpleHistory::STATUS_ERROR;

                $this->logger->add('paysimple', 'Tenant status => ' . $status);
                $this->container->get('erp.users.user.service')->addHistoryPayment($model, $status, true);
            }
        } else {
            $this->sendPaymentEmail($needCheckRecurring->getPaySimpleCustomer());
            $this->logger->add('paysimple', 'Errors from PaySimple:' . $response['errors'], 'ERROR');
            $this->logger->add(
                'paysimple',
                'Sending mail to userId=' . $needCheckRecurring->getPaySimpleCustomer()->getUser()->getId(),
                'ERROR'
            );
        }

        $needCheckRecurring->setLastCheckedDate($checkedDate);
        $this->em->persist($needCheckRecurring);
        unset($model);
    }

    /**
     * Send email on check error
     *
     * @param PaySimpleCustomer $customer
     *
     * @return bool
     */
    public function sendPaymentEmail(PaySimpleCustomer $customer)
    {
        $fullName = '';
        $id = '';
        $customerInfo = $this->container->get('erp.users.user.service')->getCustomerInfo($customer->getUser());
        if ($customerInfo && isset($customerInfo['data'])) {
            $fullName = $customerInfo['data']['FirstName'] . ' ' . $customerInfo['data']['LastName'];
            $id = $customerInfo['data']['Id'];
        }

        $email = $this->container->get('erp.core.fee.service')->getSystemEmail();
        $emailParams = ['sendTo' => $email, 'customerName' => $fullName, 'customerId' => $id];
        $emailType = EmailNotificationFactory::TYPE_PS_CHECK_ERROR;
        $sentStatus = $this->container->get('erp.core.email_notification.service')->sendEmail($emailType, $emailParams);

        return $sentStatus;
    }

    /**
     * Send email with bank account data
     *
     * @param BankAccountModel $model
     * @param User             $user
     *
     * @return bool
     */
    public function sendBankAccountToEmail(BankAccountModel $model, User $user)
    {
        $email = $this->container->get('erp.core.fee.service')->getSystemEmail();
        $emailParams = [
            'sendTo' => $email,
            'customerName' => $model->getFirstName() . ' ' . $model->getLastName(),
            'routingNumber' => $model->getRoutingNumber(),
            'accountNumber' => $model->getAccountNumber(),
            'bankName' => $model->getBankName(),
            'customerEmail' => $user->getEmail(),
        ];
        $emailType = EmailNotificationFactory::TYPE_PS_BANK_ACCOUNT;
        $sentStatus = $this->container->get('erp.core.email_notification.service')->sendEmail($emailType, $emailParams);

        return $sentStatus;
    }
}
