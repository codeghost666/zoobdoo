<?php

namespace Erp\PaymentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Erp\PaymentBundle\Entity\PaySimpleCustomer;

/**
 * PaySimpleDeferredPayments
 *
 * @ORM\Table(name="ps_deferred_payments")
 * @ORM\Entity(repositoryClass="Erp\PaymentBundle\Repository\PaySimpleDeferredPaymentsRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class PaySimpleDeferredPayments
{
    const STATUS_PENDING    = 'Pending';
    const STATUS_POSTED     = 'Posted';
    const STATUS_SETTLED    = 'Settled';
    const STATUS_AUTHORIZED = 'Authorized';
    const STATUS_FAILED     = 'Failed';

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
     * @ORM\Column(name="account_id", type="integer")
     */
    protected $accountId;

    /**
     * @var int
     *
     * @ORM\Column(name="transaction_id", type="integer", nullable=true)
     */
    protected $transactionId;

    /**
     * @var PaySimpleCustomer
     *
     * @ORM\ManyToOne(targetEntity="\Erp\PaymentBundle\Entity\PaySimpleCustomer", inversedBy="psDeferredPayments")
     * @ORM\JoinColumn(name="ps_customer_id", referencedColumnName="id", nullable=true)
     */
    protected $paySimpleCustomer;

    /**
     * @var float
     *
     * @ORM\Column(name="amount", type="float", scale=2)
     */
    protected $amount;

    /**
     * @var float
     *
     * @ORM\Column(name="allowance", type="float", scale=2, nullable=true)
     */
    protected $allowance;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="payment_date", type="datetime")
     */
    protected $paymentDate;

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
    protected $status = self::STATUS_PENDING;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="maked_date", type="datetime", nullable=true)
     */
    protected $makedDate;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_canceled", type="boolean")
     */
    protected $isCanceled = false;

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
     * Set accountId
     *
     * @param int $accountId
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
     * @return int
     */
    public function getAccountId()
    {
        return $this->accountId;
    }

    /**
     * Set transactionId
     *
     * @param int $transactionId
     *
     * @return $this
     */
    public function setTransactionId($transactionId)
    {
        $this->transactionId = $transactionId;

        return $this;
    }

    /**
     * Get transactionId
     *
     * @return int
     */
    public function getTransactionId()
    {
        return $this->transactionId;
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
     * Set amount
     *
     * @param float $amount
     *
     * @return $this
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * Get amount
     *
     * @return float
     */
    public function getAmount()
    {
        return $this->amount;
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
     * Set paymentDate
     *
     * @param \DateTime $paymentDate
     *
     * @return $this
     */
    public function setPaymentDate($paymentDate)
    {
        $this->paymentDate = $paymentDate;

        return $this;
    }

    /**
     * Get paymentDate
     *
     * @return \DateTime
     */
    public function getPaymentDate()
    {
        return $this->paymentDate;
    }

    /**
     * Set makeDate
     *
     * @param \DateTime $makedDate
     *
     * @return $this
     */
    public function setMakedDate($makedDate)
    {
        $this->makedDate = $makedDate;

        return $this;
    }

    /**
     * Get makeDate
     *
     * @return \DateTime
     */
    public function getMakedDate()
    {
        return $this->makedDate;
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
     * Set isCanceled
     *
     * @param boolean $isCanceled
     *
     * @return $this
     */
    public function setIsCanceled($isCanceled)
    {
        $this->isCanceled = $isCanceled;

        return $this;
    }

    /**
     * Get isCanceled
     *
     * @return boolean
     */
    public function getIsCanceled()
    {
        return $this->isCanceled;
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
