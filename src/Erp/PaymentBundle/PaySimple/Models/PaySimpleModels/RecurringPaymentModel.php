<?php

namespace Erp\PaymentBundle\PaySimple\Models\PaySimpleModels;

use Erp\PaymentBundle\Entity\PaySimpleCustomer;
use Erp\PaymentBundle\PaySimple\Models\PaySimpleModelInterface;
use Erp\PaymentBundle\Entity\PaySimpleRecurringPayment;

/**
 * Class RecurringPaymentModel
 *
 * @package Erp\PaymentBundle\PaySimple\Models\PaySimpleModels
 */
class RecurringPaymentModel implements PaySimpleModelInterface
{
    /**
     * @var float
     */
    protected $amount = 0;

    /**
     * @var float
     */
    protected $allowance = 0;

    /**
     * @var \DateTime
     */
    protected $startDate;

    /**
     * @var int
     */
    protected $executionFrequencyType = 5;

    /**
     * @var PaySimpleCustomer
     */
    protected $customer;

    /**
     * @var PaySimpleRecurringPayment
     */
    protected $psReccuringPayment = null;

    /**
     * @var int
     */
    protected $accountId = null;

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
     * Set expDate
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
     * Get expDate
     *
     * @return \DateTime
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * Set executionFrequencyType
     *
     * @param int $executionFrequencyType
     *
     * @return $this
     */
    public function setExecutionFrequencyType($executionFrequencyType)
    {
        $this->executionFrequencyType = $executionFrequencyType;

        return $this;
    }

    /**
     * Get executionFrequencyType
     *
     * @return int
     */
    public function getExecutionFrequencyType()
    {
        return $this->executionFrequencyType;
    }

    /**
     * @param PaySimpleCustomer $customer
     *
     * @return $this
     */
    public function setCustomer(PaySimpleCustomer $customer)
    {
        $this->customer = $customer;

        return $this;
    }

    /**
     * @return PaySimpleCustomer
     */
    public function getCustomer()
    {
        return $this->customer;
    }

    /**
     * Set reccuringPayment
     *
     * @param PaySimpleRecurringPayment $paySimpleRecurringPayment
     *
     * @return $this
     */
    public function setPsReccuringPayment(PaySimpleRecurringPayment $paySimpleRecurringPayment = null)
    {
        $this->psReccuringPayment = $paySimpleRecurringPayment;

        return $this;
    }

    /**
     * Get reccuringPayment
     *
     * @return PaySimpleRecurringPayment
     */
    public function getPsReccuringPayment()
    {
        return $this->psReccuringPayment;
    }

    /**
     * Set accountId
     *
     * @param string $accountId
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
     * @return string
     */
    public function getAccountId()
    {
        return $this->accountId;
    }

    /**
     * @return string
     */
    public function getFirstName()
    {
        return '';
    }

    /**
     * @return string
     */
    public function getLastName()
    {
        return '';
    }
}
