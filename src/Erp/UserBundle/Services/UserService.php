<?php
namespace Erp\UserBundle\Services;

use Erp\CoreBundle\EmailNotification\EmailNotificationFactory;
use Erp\CoreBundle\Entity\EmailNotification;
use Erp\PaymentBundle\Entity\PaySimpleDeferredPayments;
use Erp\PaymentBundle\Entity\PaySimpleHistory;
use Erp\PaymentBundle\Entity\PaySimpleRecurringPayment;
use Erp\PaymentBundle\PaySimple\Models\PaySimpleModels\CreditCardModel;
use Erp\PaymentBundle\PaySimple\Models\PaySimpleModels\BankAccountModel;
use Erp\PaymentBundle\PaySimple\Models\PaySimpleModelInterface;
use Erp\PaymentBundle\PaySimple\Models\PaySimpleModels\RecurringPaymentModel;
use Erp\UserBundle\Entity\User;
use Erp\UserBundle\Entity\Message;
use Erp\PaymentBundle\Entity\PaySimpleCustomer;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Erp\PaymentBundle\PaySimple\Models\PaySimpleModels\CustomerModel;
use Erp\PaymentBundle\PaySimple\Managers\PaySimpleManagerFactory;
use Erp\PaymentBundle\PaySimple\Managers\PaySimpleManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UserService
{
    const CACHE_KEY_CUTOMERS_INFO = 'ps_customers';

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $em;

    /**
     * @var \PHY\CacheBundle\Cache
     */
    protected $apcCache;

    /**
     * @var \Erp\CoreBundle\Services\Logger
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
     * Get service request types
     *
     * @return array
     */
    public function getServiceRequestTypes()
    {
        return [
            1 => 'Plumbing',
            2 => 'Electrical',
            3 => 'Heating/Cooling',
            4 => 'Cosmetic',
            5 => 'Other'
        ];
    }

    /**
     * Return settings (email notifications)
     *
     * @return array
     */
    public function getSettings()
    {
        $emailNotifications = $this->em->getRepository('ErpCoreBundle:EmailNotification')->findAll();

        if (!$emailNotifications) {
            throw new NotFoundHttpException('Email notifications not found');
        }

        /** @var EmailNotification $emailNotification */
        foreach ($emailNotifications as $emailNotification) {
            $result[$emailNotification->getType()] = $emailNotification->getName();
        }

        return $result;
    }

    /**
     * Activate user
     *
     * @param User $user
     */
    public function activateUser(User $user)
    {
        $this->em->persist($user->setEnabled(true)->setStatus(User::STATUS_ACTIVE));
        $this->em->flush();
    }

    /**
     * Deactivate user
     *
     * @param User $user
     * @param bool $flush
     * @return $this
     */
    public function deactivateUser(User $user, $flush = true, User $initiator = null)
    {
        $emailParams = [
            'sendTo' => $user->getEmail(),
            'url' => $this->container->get('router')->generate('erp_site_contact_page', [], true),
        ];
        if ($initiator) {
            $emailParams['mailFromTitle'] = $initiator->getFromForEmail();
            $emailParams['preSubject'] = $initiator->getSubjectForEmail();
        }

        $emailType = EmailNotificationFactory::TYPE_USER_DEACTIVATE;
        $this->container->get('erp.core.email_notification.service')->sendEmail($emailType, $emailParams);

        $user
            ->setEnabled(false)
            ->setStatus(User::STATUS_DISABLED);

        $this->em->persist($user);

        if ($flush) {
            $this->em->flush();
        }

        return $this;
    }

    /**
     * Deactivate all Tenants payments
     *
     * @param User $user
     *
     * @return $this
     */
    public function deactivateUserPayments(User $user)
    {
        /** @var $customer \Erp\PaymentBundle\Entity\PaySimpleCustomer */
        $customer = $user->getPaySimpleCustomers()->first();
        if ($customer) {
            $psReccuring = $this->em->getRepository('ErpPaymentBundle:PaySimpleRecurringPayment')->findBy(
                ['paySimpleCustomer' => $customer, 'status' => PaySimpleRecurringPayment::STATUS_ACTIVE]
            );

            foreach ($psReccuring as $reccuring) {
                $model = new RecurringPaymentModel();
                $model->setCustomer($customer)
                    ->setPsReccuringPayment($reccuring)
                    ->setStartDate(new \DateTime());
                $this->suspendRecurring($model);
            }

            if ($user->hasRole(User::ROLE_TENANT)) {
                $this->deactivateTenantsDeferredPayments($user);
            }

            $this->deleteCustomerRecords($user);
        }

        return $this;
    }

    /**
     * @param User $user
     *
     * @return User
     */
    public function deleteCustomerRecords(User $user)
    {
        $customer = $user->getPaySimpleCustomers()->first();
        if ($customer) {
            $object = $this->em->getRepository('ErpPaymentBundle:PaySimpleCustomer')->findOneBy(['id' => $customer]);
            $this->em->remove($object);
            $this->em->flush();
        }

        return $user;
    }

    /**
     * Deactivate Tenants deferred payments
     *
     * @param \Erp\UserBundle\Entity\User $user
     *
     * @return $this
     */
    public function deactivateTenantsDeferredPayments(User $user)
    {
        /** @var $customer \Erp\PaymentBundle\Entity\PaySimpleCustomer */
        $customer = $user->getPaySimpleCustomers()->first();
        if ($customer) {
            $psDeferred = $this->em->getRepository('ErpPaymentBundle:PaySimpleDeferredPayments')->findBy(
                ['paySimpleCustomer' => $customer, 'makedDate' => null]
            );
            /** @var $deferred \Erp\PaymentBundle\Entity\PaySimpleDeferredPayments */
            foreach ($psDeferred as $deferred) {
                $deferred->setMakedDate(new \DateTime())
                    ->setIsCanceled(true);
                $this->em->persist($deferred);
            }
            $this->em->flush();
        }

        return $this;
    }

    /**
     * Create PaySimple Customer
     *
     * @param User $user
     *
     * @return array
     */
    public function createPaySimpleCustomer(User $user)
    {
        $paySimpleCredentials = $this->getCurrentPaySimpleCredentials($user);

        $paySimpleManager = PaySimpleManagerFactory::getManagerInstance(
            PaySimpleManagerInterface::TYPE_CUSTOMER,
            $this->container,
            $paySimpleCredentials['paySimpleLogin'],
            $paySimpleCredentials['paySimpleApiSecretKey']
        );

        $response = $paySimpleManager->setModel($this->getPaySimpleCustomerModel($user))->proccess(
            PaySimpleManagerInterface::METHOD_CUSTOMER_CREATE
        );

        $result['status'] = true;
        if ($response['status'] === PaySimpleManagerInterface::STATUS_OK) {
            $psCustomer = new PaySimpleCustomer();
            $psCustomer->setUser($user)
                ->setCustomerId($response['data']['Id']);
            $this->em->persist($psCustomer);
            $this->em->flush();

            $result['data'] = $psCustomer;
        } else {
            $result['status'] = false;
            $result['errors'] = $response['errors'];
        }

        return $result;
    }

    /**
     * @param User $user
     * @param      $data
     *
     * @return mixed
     */
    public function createPaySimpleCustomerAnonymous(User $user, $data)
    {
        $paySimpleCredentials = $this->getCurrentPaySimpleCredentials($user);

        $paySimpleManager = PaySimpleManagerFactory::getManagerInstance(
            PaySimpleManagerInterface::TYPE_CUSTOMER,
            $this->container,
            $paySimpleCredentials['paySimpleLogin'],
            $paySimpleCredentials['paySimpleApiSecretKey']
        );

        $model = $this->getPaySimpleCustomerModelAnonymous($data);
        $response = $paySimpleManager->setModel($model)->proccess(
            PaySimpleManagerInterface::METHOD_CUSTOMER_CREATE
        );

        $result['status'] = true;

        if ($response['status'] === PaySimpleManagerInterface::STATUS_OK) {
            $result['psCustomer'] = $response['data'];
        } else {
            $result['status'] = false;
            $result['errors'] = $response['errors'];
        }

        return $result;
    }

    /**
     * Create PaySimple Customer
     *
     * @param PaySimpleModelInterface $model
     * @param string                  $type
     *
     * @return array
     */
    public function createPaySimplePayment(PaySimpleModelInterface $model, $type)
    {
        $paySimpleCredentials = $this->getCurrentPaySimpleCredentials($model->getCustomer()->getUser());

        $paySimpleManager = PaySimpleManagerFactory::getManagerInstance(
            PaySimpleManagerInterface::TYPE_PAYMENT,
            $this->container,
            $paySimpleCredentials['paySimpleLogin'],
            $paySimpleCredentials['paySimpleApiSecretKey']
        );

        $method = $type === PaySimpleManagerInterface::CREDIT_CARD
            ? PaySimpleManagerInterface::METHOD_PAYMENT_CREATE_CC
            : PaySimpleManagerInterface::METHOD_PAYMENT_CREATE_BA;

        $response = $paySimpleManager->setModel($model)->proccess($method);
        $result['status'] = true;

        if ($response['status'] === PaySimpleManagerInterface::STATUS_OK) {
            $psCustomer = $model->getCustomer();
            $user = $psCustomer->getUser();
            $modelMethodSet = 'set' . strtoupper($type) . 'Id';
            $modelMethodGet = 'get' . strtoupper($type).'Id';

            $isFirst = !$psCustomer->{$modelMethodGet}() && !$psCustomer->getPrimaryType();
            $isType = $psCustomer->{$modelMethodGet}() && $psCustomer->getPrimaryType() === $type;
            $isHasRecurring = !$psCustomer->getPsRecurringPayments()->first() && $user->hasRole(User::ROLE_MANAGER);

            if ($isType || $isHasRecurring || $isFirst) {
                $psCustomer->setPrimaryType($type);
            }
            $psCustomer->{$modelMethodSet}($response['data']['Id']);

            $this->em->persist($psCustomer);
            $this->em->flush();

            if (!$response['data']['IsDefault']) {
                $paySimpleManager = PaySimpleManagerFactory::getManagerInstance(
                    PaySimpleManagerInterface::TYPE_PAYMENT,
                    $this->container
                );
                $paySimpleManager->setModel($model)->proccess(
                    PaySimpleManagerInterface::METHOD_PAYMENT_SET_DEFAULT_PAYMENT_ACCOUNT
                );
            }

            $result['data'] = $psCustomer;
        } else {
            $result['status'] = false;
            $result['errors'] = $response['errors'];
        }

        return $result;
    }

    /**
     * @param User  $user
     * @param array $data
     *
     * @return mixed
     */
    public function createPaySimplePaymentAnonymous(User $user, $data)
    {
        $paySimpleCredentials = $this->getCurrentPaySimpleCredentials($user);

        $paySimpleManager = PaySimpleManagerFactory::getManagerInstance(
            PaySimpleManagerInterface::TYPE_PAYMENT,
            $this->container,
            $paySimpleCredentials['paySimpleLogin'],
            $paySimpleCredentials['paySimpleApiSecretKey']
        );

        $creditCardModel = new CreditCardModel();
        $creditCardModel
            ->setCustomer((new PaySimpleCustomer())->setCustomerId($data['customerId']))
            ->setFirstName($data['firstName'])
            ->setLastName($data['lastName'])
            ->setNumber($data['number'])
            ->setExpMonths($data['expMonths'])
            ->setExpYear($data['expYear'])
            ->setIssuer($data['issuer'])
            ->setBillingZipCode($data['zipCode'])
        ;

        $response = $paySimpleManager->setModel($creditCardModel)
            ->proccess(PaySimpleManagerInterface::METHOD_PAYMENT_CREATE_CC);

        $result['status'] = true;
        if ($response['status'] === PaySimpleManagerInterface::STATUS_OK) {
            $result['psPaymentCreateCc'] = $response['data'];
        } else {
            $result['status'] = false;
            $result['errors'] = $response['errors'];
        }

        return $result;
    }

    /**
     * Create PaySimple Customer
     *
     * @param RecurringPaymentModel $model
     *
     * @return array
     */
    public function createPaySimplePaymentRecurring(RecurringPaymentModel $model)
    {
        $isLastRecurrring = false;
        if ($model->getPsReccuringPayment()) {
            $lastRecurringResponse = $this->suspendRecurring($model);
            $isLastRecurrring = true;
        }

        // Make one-check-payment
        $customer = $model->getCustomer();

        if ($customer->getPrimaryType() === PaySimpleManagerInterface::CREDIT_CARD) {
            $checkPaymentFee = $this->container->get('erp.core.fee.service')->getCheckPaymentFee();

            $paymentModel = new RecurringPaymentModel();
            $paymentModel->setAmount($checkPaymentFee)
                ->setCustomer($customer)
                ->setStartDate(new \DateTime())
                ->setAccountId($customer->getCcId());

            $paymentResponse = $this->makeOnePayment($paymentModel);
        } else {
            $paymentResponse['status'] = true;
        }

        $this->logger->add('paysimple', 'PaySimple Payment Response => ' . json_encode($paymentResponse), 'INFO');
        if ((!$isLastRecurrring || ($isLastRecurrring && $lastRecurringResponse['status']))
             && $paymentResponse['status']
        ) {
            $paySimpleCredentials = $this->getCurrentPaySimpleCredentials($model->getCustomer()->getUser());

            $paySimpleManager = PaySimpleManagerFactory::getManagerInstance(
                PaySimpleManagerInterface::TYPE_RECURRING,
                $this->container,
                $paySimpleCredentials['paySimpleLogin'],
                $paySimpleCredentials['paySimpleApiSecretKey']
            );

            $response = $paySimpleManager->setModel($model)->proccess(
                PaySimpleManagerInterface::METHOD_RECURRING_CREATE
            );

            $result['status'] = true;
            $this->logger->add('paysimple', 'PaySimple Payment Recurring => ' . json_encode($response), 'INFO');
            if ($response['status'] === PaySimpleManagerInterface::STATUS_OK) {
                $psReccuringPayment = new PaySimpleRecurringPayment();
                $psReccuringPayment->setPaySimpleCustomer($model->getCustomer())
                    ->setRecurringId($response['data']['Id'])
                    ->setNextDate(new \DateTime($response['data']['NextScheduleDate']))
                    ->setAccountId($response['data']['AccountId'])
                    ->setStartDate(new \DateTime($response['data']['StartDate']))
                    ->setMonthlyAmount($response['data']['PaymentAmount'])
                    ->setAllowance($model->getAllowance())
                    ->setStatus(PaySimpleRecurringPayment::STATUS_ACTIVE)
                    ->setSubscriptionType($model->getCustomer()->getPrimaryType())
                    ->setType(PaySimpleRecurringPayment::TYPE_RECURRING);

                $this->em->persist($psReccuringPayment);
                $this->em->flush();

                $result['data'] = $psReccuringPayment;

                if ($model->getCustomer()->getUser()->hasRole(User::ROLE_TENANT)
                    && $model->getStartDate()->format('Y-m-d') == (new \DateTime())->format('Y-m-d')
                ) {
                    $this->addHistoryPayment($model, PaySimpleHistory::STATUS_SUCCESS);
                }
            } else {
                $result['status'] = false;
                $result['errors'] = $response['errors'];
                if ($model->getCustomer()->getUser()->hasRole(User::ROLE_TENANT)
                    && $model->getStartDate()->format('Y-m-d') == (new \DateTime())->format('Y-m-d')
                ) {
                    $this->addHistoryPayment($model, PaySimpleHistory::STATUS_ERROR);
                }
            }
        } else {
            $result['status'] = false;
            $result['errors'] = (isset($lastRecurringResponse['errors']))
                ? $lastRecurringResponse['errors']
                : [['Message' => $this->getPaySimpleErrorByCode('one_check_payment_error')]];
        }

        return $result;
    }

    /**
     * Suspend recurring payment
     *
     * @param RecurringPaymentModel $model
     *
     * @throws PaySimpleManagerException
     */
    public function suspendRecurring(RecurringPaymentModel $model)
    {
        $paySimpleCredentials = $this->getCurrentPaySimpleCredentials($model->getCustomer()->getUser());

        $paySimpleManager = PaySimpleManagerFactory::getManagerInstance(
            PaySimpleManagerInterface::TYPE_RECURRING,
            $this->container,
            $paySimpleCredentials['paySimpleLogin'],
            $paySimpleCredentials['paySimpleApiSecretKey']
        );

        $response = $paySimpleManager->setModel($model)->proccess(
            PaySimpleManagerInterface::METHOD_RECURRING_SUSPEND
        );

        $result['status'] = true;
        $this->logger->add('paysimple', 'PaySimple Suspend Recurring => ' . json_encode($response), 'INFO');
        if ($response['status'] === PaySimpleManagerInterface::STATUS_OK) {
            $recurringResponse = $this->retrieveRecurringPayment($model);

            if ($recurringResponse['status']) {
                $suspendStatus = $recurringResponse['data']['ScheduleStatus'];
                if ($suspendStatus === 'Suspended') {
                    $lastReccuringPayment = $model->getPsReccuringPayment();
                    $lastReccuringPayment->setStatus(PaySimpleRecurringPayment::STATUS_SUSPEND);
                    $this->em->persist($lastReccuringPayment);
                    $this->em->flush();
                }
                $result['data'] = $lastReccuringPayment;
            } else {
                $result['status'] = false;
                $result['errors'] = $recurringResponse['errors'];
            }
        } else {
            $result['status'] = false;
            $result['errors'] = $response['errors'];
        }

        return $result;
    }

    /**
     * @param RecurringPaymentModel $model
     *
     * @return mixed
     * @throws \Erp\PaymentBundle\PaySimple\Exeptions\PaySimpleManagerException
     */
    public function getActiveCustomerSchedules(RecurringPaymentModel $model)
    {
        $paySimpleCredentials = $this->getCurrentPaySimpleCredentials($model->getCustomer()->getUser());

        $paySimpleManager = PaySimpleManagerFactory::getManagerInstance(
            PaySimpleManagerInterface::TYPE_CUSTOMER,
            $this->container,
            $paySimpleCredentials['paySimpleLogin'],
            $paySimpleCredentials['paySimpleApiSecretKey']
        );

        $response = $paySimpleManager->setModel($model)->proccess(
            PaySimpleManagerInterface::METHOD_CUSTOMER_GET_ACTIVE_SCHEDULES
        );

        return $response;
    }

    /**
     * @param RecurringPaymentModel $model
     *
     * @return array
     * @throws PaySimpleManagerException
     */
    public function retrieveRecurringPayment(RecurringPaymentModel $model)
    {
        $paySimpleCredentials = $this->getCurrentPaySimpleCredentials($model->getCustomer()->getUser());

        $paySimpleManager = PaySimpleManagerFactory::getManagerInstance(
            PaySimpleManagerInterface::TYPE_RECURRING,
            $this->container,
            $paySimpleCredentials['paySimpleLogin'],
            $paySimpleCredentials['paySimpleApiSecretKey']
        );

        $response = $paySimpleManager->setModel($model)->proccess(
            PaySimpleManagerInterface::METHOD_RECURRING_GET
        );

        $result['status'] = true;
        $this->logger->add('paysimple', 'PaySimple Retrive Recurring => ' . json_encode($response), 'INFO');
        if ($response['status'] === PaySimpleManagerInterface::STATUS_OK) {
            $result['data'] = $response['data'];
        } else {
            $result['status'] = false;
            $result['errors'] = $response['errors'];
        }

        return $result;
    }

    /**
     * Make one tenant payment
     *
     * @param RecurringPaymentModel     $model
     * @param PaySimpleDeferredPayments $deferredPayment
     *
     * @return array
     * @throws PaySimpleManagerException
     */
    public function makeOnePayment(RecurringPaymentModel $model, PaySimpleDeferredPayments $deferredPayment = null)
    {
        $result['status'] = true;
        if ($model->getStartDate()->format('Y-m-d') === (new \DateTime())->format('Y-m-d')) {

            $paySimpleCredentials = $this->getCurrentPaySimpleCredentials($model->getCustomer()->getUser());

            $paySimpleManager = PaySimpleManagerFactory::getManagerInstance(
                PaySimpleManagerInterface::TYPE_PAYMENT,
                $this->container,
                $paySimpleCredentials['paySimpleLogin'],
                $paySimpleCredentials['paySimpleApiSecretKey']
            )->setModel($model);

            $response = $paySimpleManager->proccess(PaySimpleManagerInterface::METHOD_PAYMENT_MAKE);

            $this->logger->add('paysimple', 'PaySimple Response => ' . json_encode($response), 'INFO');
            if ($response['status'] === PaySimpleManagerInterface::STATUS_OK
                && (
                    $response['data']['Status'] == PaySimpleDeferredPayments::STATUS_AUTHORIZED
                    || $response['data']['Status'] == PaySimpleDeferredPayments::STATUS_POSTED
                )
            ) {
                if (!$deferredPayment) {
                    $deferredPayment = new PaySimpleDeferredPayments();
                    $deferredPayment->setPaySimpleCustomer($model->getCustomer())
                        ->setAccountId($response['data']['AccountId'])
                        ->setAmount($response['data']['Amount'])
                        ->setAllowance($model->getAllowance())
                        ->setPaymentDate(new \DateTime($response['data']['PaymentDate']));
                }

                $deferredPayment->setTransactionId($response['data']['Id'])
                    ->setStatus($response['data']['Status'])
                    ->setMakedDate(new \DateTime());

                if ($model->getCustomer()->getUser()->hasRole(User::ROLE_TENANT)) {
                    $statusCC = $deferredPayment->getStatus() == PaySimpleDeferredPayments::STATUS_AUTHORIZED;
                    $statusBA = $deferredPayment->getStatus() == PaySimpleDeferredPayments::STATUS_POSTED;

                    $status = $statusCC || $statusBA ?
                        PaySimpleHistory::STATUS_SUCCESS
                        : PaySimpleHistory::STATUS_ERROR;
                    $this->addHistoryPayment($model, $status);
                }

                $this->em->persist($deferredPayment);
                $this->em->flush();

                $result['data'] = $deferredPayment;
            } else {
                $result['status'] = false;
                $result['errors'] = isset($response['errors']) ? $response['errors'] : '';
            }
        } else {
            $result['data'] = $this->addDeferredPayment($model);
        }

        return $result;
    }

    /**
     * @param User  $user
     * @param array $data
     *
     * @return mixed
     */
    public function makeOnePaymentAnonymous(User $user, $data)
    {
        $result = $this->createPaySimpleCustomerAnonymous($user, $data);

        if ($result['status']) {
            $data['customerId'] = $result['psCustomer']['Id'];
            $result = $this->createPaySimplePaymentAnonymous($user, $data);

            if ($result['status']) {
                $amount = $this->container->get('erp.core.fee.service')->getApplicationFormAnonymousFee();

                $paymentModel = new RecurringPaymentModel();
                $paymentModel->setAmount($amount)
                    ->setAccountId($result['psPaymentCreateCc']['Id']);

                $paySimpleCredentials = $this->getCurrentPaySimpleCredentials($user);

                $paySimpleManager = PaySimpleManagerFactory::getManagerInstance(
                    PaySimpleManagerInterface::TYPE_PAYMENT,
                    $this->container,
                    $paySimpleCredentials['paySimpleLogin'],
                    $paySimpleCredentials['paySimpleApiSecretKey']
                )->setModel($paymentModel);

                $response = $paySimpleManager->proccess(PaySimpleManagerInterface::METHOD_PAYMENT_MAKE);

                $this->logger->add(
                    'paysimple',
                    'PaySimple Payment Anonymous Response => ' . json_encode($response),
                    'INFO'
                );

                $result['status'] = true;
                if ($response['status'] == PaySimpleManagerInterface::STATUS_OK
                    && $response['data']['Status'] == PaySimpleDeferredPayments::STATUS_AUTHORIZED
                ) {
                    $result['psPaymentMake'] = $response['data'];
                } else {
                    $result['status'] = false;
                    $result['errors'] = isset($response['errors'])
                        ? $response['errors']
                        : [0 => ['Message' => $this->getPaySimpleErrorByCode('error')]];
                }
            }
        }

        return $result;
    }

    /**
     * Add deferred payment
     *
     * @param RecurringPaymentModel $model
     *
     * @return PaySimpleDeferredPayments
     */
    public function addDeferredPayment(RecurringPaymentModel $model)
    {
        $deferredPayment = new PaySimpleDeferredPayments();
        $deferredPayment->setPaySimpleCustomer($model->getCustomer())
            ->setAccountId($model->getAccountId())
            ->setAmount($model->getAmount())
            ->setAllowance($model->getAllowance())
            ->setPaymentDate($model->getStartDate())
            ->setStatus(PaySimpleDeferredPayments::STATUS_PENDING);

        $this->em->persist($deferredPayment);
        $this->em->flush();

        return $deferredPayment;
    }

    /**
     * Add history record
     *
     * @param RecurringPaymentModel $recurringPaymentModel
     * @param string                $status
     * @param bool                  $isWithAllowance
     *
     * @return PaySimpleHistory
     */
    public function addHistoryPayment(RecurringPaymentModel $recurringPaymentModel, $status, $isWithAllowance = false)
    {
        /* if user's manager has private paysimple account,
            we need to charge this manager */
        $paySimpleCredentials = $this->getCurrentPaySimpleCredentials($recurringPaymentModel->getCustomer()->getUser());
        $isTenant = $recurringPaymentModel->getCustomer()->getUser()->hasRole(User::ROLE_TENANT);
        if ($isTenant
            && $paySimpleCredentials['paySimpleLogin']
            && $paySimpleCredentials['paySimpleApiSecretKey']
            && $status == PaySimpleHistory::STATUS_SUCCESS
            && $recurringPaymentModel->getAllowance() > 0
        ) {
            $manager = $recurringPaymentModel->getCustomer()->getUser()->getTenantProperty()->getUser();
            $customer = $manager->getPaySimpleCustomers()->first();
            $accountId = $customer->getPrimaryType() === PaySimpleManagerInterface::CREDIT_CARD
                ? $customer->getCcId()
                : $customer->getBaId();

            $paymentModel = new RecurringPaymentModel();
            $paymentModel->setAmount($recurringPaymentModel->getAllowance())
                ->setCustomer($customer)
                ->setStartDate(new \DateTime())
                ->setAccountId($accountId);

            $this->container->get('erp.users.user.service')->makeOnePayment($paymentModel);
        }

        $amount = $recurringPaymentModel->getAmount();
        if ($isWithAllowance) {
            $amount = $recurringPaymentModel->getAmount() - $recurringPaymentModel->getAllowance();
        }
        $user = $recurringPaymentModel->getCustomer()->getUser();
        $history = new PaySimpleHistory();
        $history->setProperty($user->getTenantProperty())
            ->setPaymentDate($recurringPaymentModel->getStartDate())
            ->setAmount($amount)
            ->setStatus($status)
            ->setUser($user);

        $this->em->persist($history);
        $this->em->flush();

        return $history;
    }

    /**
     * Get Pay Simple Payment model object
     *
     * @param User   $user
     * @param string $type
     *
     * @return CreditCardModel
     */
    public function getPaySimplePaymentModel(User $user, $type)
    {
        $model = $type === PaySimpleManagerInterface::CREDIT_CARD ? new CreditCardModel() : new BankAccountModel();
        $firstName = $user->getFirstName();
        $lastName = $user->getLastName();
        $billingZipCode = $user->getPostalCode();

        $userInfo = $this->getCustomerInfo($user);
        if ($userInfo && $userInfo['status']) {
            $firstName = $userInfo['data']['FirstName'];
            $lastName = $userInfo['data']['LastName'];
            $billingZipCode = $userInfo['data']['BillingAddress']['ZipCode'];
        }

        $model->setFirstName($firstName)
            ->setLastName($lastName);

        if ($type === PaySimpleManagerInterface::CREDIT_CARD) {
            $model->setBillingZipCode($billingZipCode);
        } else {
            $model->setIsCheckingAccount(true);
        }

        return $model;
    }

    /**
     * Get info about customer payment account (credit_card|bank_account)
     *
     * @param User   $user
     * @param string $type
     *
     * @return array|null
     * @throws PaySimpleManagerException
     */
    public function getCustomerPaymentAccountInfo(User $user, $type)
    {
        $result = null;
        $userPSCustomer = $user->getPaySimpleCustomers()->first();

        if ($userPSCustomer) {
            /** @var $cache \PHY\CacheBundle\Cache */
            $cache = $this->container->get('phy_cache');
            $hashKey = $this->getCustomerHashKey($userPSCustomer, $type);
            $result = ['status' => true, 'data' => $cache->get($hashKey)];

            if ($result['data']) {
                $customerPayId = $userPSCustomer->getPrimaryType() === PaySimpleManagerInterface::CREDIT_CARD
                    ? $userPSCustomer->getCcId()
                    : $userPSCustomer->getBaId();
                if ($result['data']['Id'] !== $customerPayId) {
                    $result['data'] = [];
                    $cache->delete($hashKey);
                }
            }

            if (!$result['data']) {
                $model = (new BankAccountModel())->setCustomer($userPSCustomer);

                $paySimpleCredentials = $this->getCurrentPaySimpleCredentials($user);
                $paySimpleManager = PaySimpleManagerFactory::getManagerInstance(
                    PaySimpleManagerInterface::TYPE_PAYMENT,
                    $this->container,
                    $paySimpleCredentials['paySimpleLogin'],
                    $paySimpleCredentials['paySimpleApiSecretKey']
                );

                $response = $paySimpleManager->setModel($model)->proccess($type);

                if ($response['status'] === PaySimpleManagerInterface::STATUS_OK) {
                    $cache->set($hashKey, $response['data']);
                    $result['data'] = $response['data'];
                } else {
                    $result['status'] = false;
                    $result['errors'] = $response['errors'];
                    $cache->delete($hashKey);
                }
            }
        }

        return $result;
    }

    /**
     * Get info about customer
     *
     * @param User $user
     *
     * @return array|null
     * @throws PaySimpleManagerException
     */
    public function getCustomerInfo(User $user)
    {
        $result = null;
        $userPSCustomer = $user->getPaySimpleCustomers()->first();

        if ($userPSCustomer) {
            /** @var $cache \PHY\CacheBundle\Cache */
            $cache = $this->container->get('phy_cache');
            $hashKey = $this->getCustomerHashKey($userPSCustomer, PaySimpleManagerInterface::METHOD_CUSTOMER_GET_INFO);
            $result = ['status' => true, 'data' => $cache->get($hashKey)];

            if (!$result['data']) {
                $model = (new CustomerModel())->setCustomer($userPSCustomer);
                $paySimpleManager = PaySimpleManagerFactory::getManagerInstance(
                    PaySimpleManagerInterface::TYPE_CUSTOMER,
                    $this->container
                );
                $response = $paySimpleManager->setModel($model)->proccess(
                    PaySimpleManagerInterface::METHOD_CUSTOMER_GET_INFO
                );

                if ($response['status'] === PaySimpleManagerInterface::STATUS_OK) {
                    $cache->set($hashKey, $response['data']);
                    $result['data'] = $response['data'];
                } else {
                    $result['status'] = false;
                    $result['errors'] = $response['errors'];
                    $cache->delete($hashKey);
                }
            }
        }

        return $result;
    }

    /**
     * Get key for cache
     *
     * @param PaySimpleCustomer $customer
     * @param string            $type
     *
     * @return string
     */
    public function getCustomerHashKey(PaySimpleCustomer $customer, $type)
    {
        return self::CACHE_KEY_CUTOMERS_INFO . $customer->getCustomerId() . '[' . $type . ']';
    }

    /**
     * Return hint content
     *
     * @param User   $user
     * @param string $hintCode
     *
     * @return stirng|null
     */
    public function getHintContent(User $user, $hintCode)
    {
        $nextRecDate = 'Not set';
        if ($hintCode == 'pay_rent' || $hintCode == 'manager_bank_and_card_information') {
            $nextRecDate = $this->getNextRecurringDate($user);
        }

        $feeService = $this->container->get('erp.core.fee.service');

        $hintContent = [
            'email_options'                      => 'Here you can add and edit Email for notifications from Zoobdoo.',
            'account_login_details'              => 'Here you can edit your profile picture, name and password.',
            'my_property'                        => 'Address of Property you have rented.',
            'message_manager'                    => 'Here you can quickly send a Message to your Manager.
                You can see all messages at Messages tab.',
            'service_requests'                   => 'Use this form to request a service from your Manager and describe
                your problem.',
            'ask_a_pro_for_tip'                  => 'Should you have any questions to property management professional,
                please use this form to submit your ticket.
                Zoobdoo  will contact you shortly and provide with information on consultation cost.',
            'pay_rent'                           => 'Here you can make one-time and recurring payments to your Manager.
                Payment will be executed on specified date. All payments made with today\'s date are
                executed immediately.
                Please note that once you create new recurring payment, the old one will be cancelled.
                Next recurring payment date: ' . $nextRecDate . '. <br/><br/>Note: every time you create a recurring
                payment, you will be charged a card verification fee of $' . $feeService->getCheckPaymentFee() . '.',
            'tenant_bank_and_card_information'   => 'Here you can add credit card or bank account information.
                In order to be able to pay rent at least one payment method should be entered.',
            'manager_bank_and_card_information' => 'Here you can add credit card or bank account information.
                In order to use all features of the website at least one payment method should be entered.
                Once you add valid credit card or bank account, you will be charged recurring fee on a monthly basis.
                Next recurring payment date: ' . $nextRecDate,
            'payment_preferences'                => 'Please select Primary Account which will be used for
                all payments at Zoobdoo. ',
            'address_details'                    => 'Please enter your valid address details and phone number.
                This information will also be used for billing.',
            'background_check_credit_check'      => 'Here you can conduct Tenant screening and get Background Check
                and Credit Check reports.
                Enter Tenant email and press GO to initiate the process. Tenant will receive emails with links to
                registration form and IDMA exam. Once Tenant passes the exam, select his/her email in dropdown and
                click "GET REPORT" button',
            'payment_history'                    => 'Here you can see latest rental payments made by your
                Tenants and export full report to CSV or PDF for analysis.',
            'manager_settings'                  => 'Use these settings to subscribe or unsubscribe from certain types
                of notifications. You can enter different Email address here if you want to send these notifications
                to other (secondary) Email address than your primary Email.'
        ];

        return (isset($hintContent[$hintCode]) ? $hintContent[$hintCode] : null);
    }

    /**
     * Get next recurring payment date
     *
     * @param User $user
     *
     * @return string
     */
    protected function getNextRecurringDate(User $user)
    {
        $nextRecurringDate = 'Not set';
        $psCustomer = $user->getPaySimpleCustomers()->first();
        if ($psCustomer) {
            $lastRecurring = $this->em->getRepository('ErpPaymentBundle:PaySimpleRecurringPayment')->findOneBy(
                ['paySimpleCustomer' => $psCustomer, 'status' => PaySimpleRecurringPayment::STATUS_ACTIVE]
            );
            if ($lastRecurring) {
                $nextRecurringDate = $lastRecurring->getNextDate()->format('m/d/Y');
            }
        }

        return $nextRecurringDate;
    }

    /**
     * Get PaySimple Customer object
     *
     * @param \Erp\UserBundle\Entity\User $user
     *
     * @return \Erp\PaymentBundle\PaySimple\Models\PaySimpleModels\CustomerModel
     */
    private function getPaySimpleCustomerModel(User $user)
    {
        $userRole = $user->hasRole(User::ROLE_MANAGER) ? User::ROLE_MANAGER : User::ROLE_TENANT;

        $newCustomer = new CustomerModel();
        $newCustomer->setCustomerAccount($user->getId())
            ->setFirstName($user->getFirstName())
            ->setLastName($user->getLastName())
            ->setPhone($user->getPhoneDigitsOnly())
            ->setEmail($user->getEmail())
            ->setBStreetAddress1($user->getAddressOne())
            ->setBCity($user->getCity()->getName())
            ->setBStateCode($user->getCity()->getStateCode())
            ->setBZipCode($user->getPostalCode())
            ->setBCountry('US')
            ->setNotes($userRole . '[id=' . $user->getId() . ']');

        if ($user->getCompanyName()) {
            $newCustomer->setCompany($user->getCompanyName());
        }
        if ($user->getWebsiteUrl()) {
            $newCustomer->setWebSite($user->getWebsiteUrl());
        }

        return $newCustomer;
    }

    /**
     * @param array $data
     *
     * @return CustomerModel
     */
    public function getPaySimpleCustomerModelAnonymous($data)
    {
        $newCustomer = new CustomerModel();
        $newCustomer
            ->setFirstName($data['firstName'])
            ->setLastName($data['lastName'])
            ->setPhone(str_replace('-', '', $data['phone']))
            ->setBStreetAddress1($data['address'])
            ->setBCity($data['city'])
            ->setBStateCode(strtoupper($data['stateCode']))
            ->setBZipCode($data['zipCode'])
            ->setBCountry('US')
            ->setNotes('Anonymous');

        if (isset($data['email'])) {
            $newCustomer->setEmail($data['email']);
        }

        return $newCustomer;
    }

    /**
     * Get error message by code
     *
     * @param $code
     *
     * @return mixed
     */
    public function getPaySimpleErrorByCode($code)
    {
        $errors = [
            'import_properties_ok'        => 'Payment was successful. Properties were imported.',
            'create_property_ok'          => 'Payment was successful. You can create one more Property.',
            'create_application_form_ok'  => 'Payment was successful. You can create Online Rental Application Form.',
            'create_contract_form_ok'     => 'Payment was successful. You can create Online Rental Contract Form.',
            'charge_repost_request_ok'    => 'Payment was successful.
                Request to post this vacancy online was authorized and sent to Administrator.',
            'charge_esign_tenant_ok'      => 'Payment was successful.',
            'pay_rent_tenant_ok'          => 'Payment has been successfully created.
                Processing your payment may take some time.',
            'pay_rent_recurring_ok'       => 'Recurring payment schedule was created successfully.',
            'charge_esign_tenant_error'   => 'An error occurred while trying to sign a document.
                Please contact your Manager.',
            'error'                       => 'An error occurred while charging. Possible reasons: too many transactions
                in short time, wrong payment settings, not valid card/bank account details, not enough funds.
                Please try again several minutes later.',
            'one_check_payment_error'     => 'Your card/bank account has not enough funds or the bank has declined
                transaction by other reason.',
        ];

        return $errors[$code];
    }

    /**
     * Set status for unread messages on read
     *
     * @param User $user
     */
    public function setStatusUnreadMessages(User $user)
    {
        $messages = $this->em->getRepository('ErpUserBundle:Message')->findBy(['fromUser' => $user, 'isRead' => false]);

        /** @var Message $message */
        foreach ($messages as $message) {
            $message->setIsRead(true);
            $this->em->persist($message);
        }

        $this->em->flush();

        return;
    }

    /**
     * @param string $paySimpleLogin
     * @param string $paySimpleApiSecretKey
     *
     * @return mixed
     * @throws \Erp\PaymentBundle\PaySimple\Exeptions\PaySimpleManagerException
     */
    public function validateCredentials($paySimpleLogin, $paySimpleApiSecretKey)
    {
        $paySimpleManager = PaySimpleManagerFactory::getManagerInstance(
            PaySimpleManagerInterface::TYPE_CUSTOMER,
            $this->container,
            $paySimpleLogin,
            $paySimpleApiSecretKey
        );

        $response = $paySimpleManager->proccess(PaySimpleManagerInterface::METHOD_CUSTOMER_GET_LIST_CUSTOMERS);

        return $response['code'] != Response::HTTP_UNAUTHORIZED;
    }

    /**
     * @param User $user
     *
     * @return array
     */
    public function getCurrentPaySimpleCredentials(User $user)
    {
        $credentials = [
            'paySimpleLogin' => null,
            'paySimpleApiSecretKey' => null,
        ];

        if ($user->hasRole(User::ROLE_TENANT) || $user->hasRole(User::ROLE_ANONYMOUS)) {
            if ($user->hasRole(User::ROLE_TENANT)) {
                /** @var User $user */
                $user = $user->getTenantProperty()->getUser();
            }

            if ($user->getIsPrivatePaySimple()) {
                $credentials = [
                    'paySimpleLogin' => $user->getPaySimpleLogin(),
                    'paySimpleApiSecretKey' => $user->getPaySimpleApiSecretKey(),
                ];
            }
        }

        return $credentials;
    }
}
