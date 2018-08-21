<?php

namespace Erp\PaymentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Erp\PaymentBundle\Entity\PaySimpleCustomer;

/**
 * Class PaySimpleRecurringPayment
 *
 * @ORM\Table(name="ps_recurring_payment")
 * @ORM\Entity(repositoryClass="Erp\PaymentBundle\Repository\PaySimpleRecurringPaymentRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class PaySimpleRecurringPayment
{
    const STATUS_EXPIRED = 'Expired';
    const STATUS_ACTIVE  = 'Active';
    const STATUS_SUSPEND = 'Suspended';
    const STATUS_PAUSE   = 'PauseUntil';

    const TYPE_ONE       = 'one';
    const TYPE_RECURRING = 'recurring';

    const SUBSCRIPTION_TYPE_CC = 'cc';
    const SUBSCRIPTION_TYPE_BA = 'ba';

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var int
     *
     * @ORM\Column(name="recurring_id", type="integer")
     */
    protected $recurringId;

    /**
     * @var PaySimpleCustomer
     *
     * @ORM\ManyToOne(targetEntity="\Erp\PaymentBundle\Entity\PaySimpleCustomer", inversedBy="psRecurringPayments")
     * @ORM\JoinColumn(name="ps_customer_id", referencedColumnName="id")
     */
    protected $paySimpleCustomer;

    /**
     * @var float
     *
     * @ORM\Column(name="monthly_amount", type="float", scale=2, nullable=true)
     */
    protected $monthlyAmount;

    /**
     * @var float
     *
     * @ORM\Column(name="allowance", type="float", scale=2, nullable=true)
     */
    protected $allowance;

    /**
     * @var string
     *
     * @ORM\Column(
     *      name="subscription_type",
     *      type="string",
     *      nullable=true
     * )
     */
    protected $subscriptionType = self::SUBSCRIPTION_TYPE_CC;

    /**
     * @var int
     *
     * @ORM\Column(name="account_id", type="integer")
     */
    protected $accountId;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="start_date", type="datetime")
     */
    protected $startDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="next_date", type="datetime")
     */
    protected $nextDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="last_checked_date", type="datetime", nullable=true)
     */
    protected $lastCheckedDate;

    /**
     * @var string
     *
     * @ORM\Column(
     *      name="status",
     *      type="string",
     *      length=16,
     *      nullable=true
     * )
     */
    protected $status = self::STATUS_ACTIVE;

    /**
     * @var string
     *
     * @ORM\Column(
     *      name="type",
     *      type="string",
     *      nullable=true
     * )
     */
    protected $type = self::TYPE_RECURRING;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_date", type="datetime")
     */
    protected $createdDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_date", type="datetime")
     */
    protected $updatedDate;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set recurringId
     *
     * @param integer $recurringId
     *
     * @return $this
     */
    public function setRecurringId($recurringId)
    {
        $this->recurringId = $recurringId;

        return $this;
    }

    /**
     * Get recurringId
     *
     * @return integer
     */
    public function getRecurringId()
    {
        return $this->recurringId;
    }

    /**
     * Set paySimpleCustomer
     *
     * @param PaySimpleCustomer $paySimpleCustomer
     *
     * @return $this
     */
    public function setPaySimpleCustomer(PaySimpleCustomer $paySimpleCustomer)
    {
        $this->paySimpleCustomer = $paySimpleCustomer;

        return $this;
    }

    /**
     * Get paySimpleCustomer
     *
     * @return PaySimpleCustomer
     */
    public function getPaySimpleCustomer()
    {
        return $this->paySimpleCustomer;
    }

    /**
     * Set monthlyAmount
     *
     * @param float $monthlyAmount
     *
     * @return $this
     */
    public function setMonthlyAmount($monthlyAmount)
    {
        $this->monthlyAmount = $monthlyAmount;

        return $this;
    }

    /**
     * Get monthlyAmount
     *
     * @return float
     */
    public function getMonthlyAmount()
    {
        return $this->monthlyAmount;
    }

    /**
     * Set allowance
     *
     * @param float $allowance
     *
     * @return $this
     */
    public function setAllowance($allowance)
    {
        $this->allowance = $allowance;

        return $this;
    }

    /**
     * Get allowance
     *
     * @return float
     */
    public function getAllowance()
    {
        return $this->allowance;
    }

    /**
     * Set subscriptionType
     *
     * @param string $subscriptionType
     *
     * @return $this
     */
    public function setSubscriptionType($subscriptionType)
    {
        $this->subscriptionType = $subscriptionType;

        return $this;
    }

    /**
     * Get subscriptionType
     *
     * @return string
     */
    public function getSubscriptionType()
    {
        return $this->subscriptionType;
    }

    /**
     * Set accountId
     *
     * @param integer $accountId
     *
     * @return $this
     */
    public function setAccountId($accountId)
    {
        $this->accountId = $accountId;

        return $this;
    }

    /**
     * Get accountId
     *
     * @return integer
     */
    public function getAccountId()
    {
        return $this->accountId;
    }

    /**
     * Set startDate
     *
     * @param \DateTime $startDate
     *
     * @return $this
     */
    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;

        return $this;
    }

    /**
     * Get startDate
     *
     * @return \DateTime
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * Set nextDate
     *
     * @param \DateTime $nextDate
     *
     * @return $this
     */
    public function setNextDate($nextDate)
    {
        $this->nextDate = $nextDate;

        return $this;
    }

    /**
     * Get nextDate
     *
     * @return \DateTime
     */
    public function getNextDate()
    {
        return $this->nextDate;
    }

    /**
     * Set lastCheckedDate
     *
     * @param \DateTime $lastCheckedDate
     *
     * @return $this
     */
    public function setLastCheckedDate($lastCheckedDate)
    {
        $this->lastCheckedDate = $lastCheckedDate;

        return $this;
    }

    /**
     * Get lastCheckedDate
     *
     * @return \DateTime
     */
    public function getLastCheckedDate()
    {
        return $this->lastCheckedDate;
    }

    /**
     * Set status
     *
     * @param string $status
     *
     * @return $this
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set type
     *
     * @param string $type
     *
     * @return $this
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set createdDate
     *
     * @ORM\PrePersist
     */
    public function setCreatedDate()
    {
        $this->createdDate = new \DateTime();
    }

    /**
     * Get createdDate
     *
     * @return \DateTime
     */
    public function getCreatedDate()
    {
        return $this->createdDate;
    }

    /**
     * Set updatedDate
     *
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function setUpdatedDate()
    {
        $this->updatedDate = new \DateTime();
    }

    /**
     * Get updatedDate
     *
     * @return \DateTime
     */
    public function getUpdatedDate()
    {
        return $this->updatedDate;
    }
}
