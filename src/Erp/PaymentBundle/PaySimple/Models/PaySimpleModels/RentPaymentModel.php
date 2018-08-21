<?php

namespace Erp\PaymentBundle\PaySimple\Models\PaySimpleModels;

use Erp\PaymentBundle\Entity\PaySimpleCustomer;
use Erp\PaymentBundle\PaySimple\Models\PaySimpleModelInterface;

/**
 * Class RentPaymentModel
 *
 * @package Erp\PaymentBundle\PaySimple\Models\PaySimpleModels
 */
class RentPaymentModel implements PaySimpleModelInterface
{
    /**
     * @var float
     */
    protected $amount;

    /**
     * @var \DateTime
     */
    protected $startDate;

    /**
     * @var PaySimpleCustomer
     */
    protected $customer;

    /**
     * @var boolean
     */
    protected $isRecurring = false;

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
     * Set isRecurring
     *
     * @param boolean $isRecurring
     *
     * @return $this
     */
    public function setIsRecurring($isRecurring)
    {
        $this->isRecurring = $isRecurring;

        return $this;
    }

    /**
     * Get isRecurring
     *
     * @return boolean
     */
    public function getIsRecurring()
    {
        return $this->isRecurring;
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
