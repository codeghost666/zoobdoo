<?php

namespace Erp\PaymentBundle\PaySimple\Models\PaySimpleModels;

use Erp\PaymentBundle\Entity\PaySimpleCustomer;
use Erp\PaymentBundle\PaySimple\Models\PaySimpleModelInterface;

/**
 * Class BankAccountModel
 *
 * @package Erp\PaymentBundle\Entity
 */
class BankAccountModel implements PaySimpleModelInterface
{
    /**
     * @var string
     */
    protected $firstName;

    /**
     * @var string
     */
    protected $lastName;

    /**
     * @var string
     */
    protected $routingNumber;

    /**
     * @var string
     */
    protected $accountNumber;

    /**
     * @var boolean
     */
    protected $isCheckingAccount;

    /**
     * @var string
     */
    protected $bankName;

    /**
     * @var PaySimpleCustomer
     */
    protected $customer;

    /**
     * Set firstName
     *
     * @param string $firstName
     *
     * @return $this
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * Get firstName
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set lastName
     *
     * @param string $lastName
     *
     * @return $this
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * Get lastName
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
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
     * Set routingNumber
     *
     * @param string $routingNumber
     *
     * @return $this
     */
    public function setRoutingNumber($routingNumber)
    {
        $this->routingNumber = $routingNumber;

        return $this;
    }

    /**
     * Get routingNumber
     *
     * @return string
     */
    public function getRoutingNumber()
    {
        return $this->routingNumber;
    }

    /**
     * Set accountNumber
     *
     * @param string $accountNumber
     *
     * @return $this
     */
    public function setAccountNumber($accountNumber)
    {
        $this->accountNumber = $accountNumber;

        return $this;
    }

    /**
     * Get accountNumber
     *
     * @return string
     */
    public function getAccountNumber()
    {
        return $this->accountNumber;
    }

    /**
     * Set isCheckingAccount
     *
     * @param boolean $isCheckingAccount
     *
     * @return $this
     */
    public function setIsCheckingAccount($isCheckingAccount)
    {
        $this->isCheckingAccount = $isCheckingAccount;

        return $this;
    }

    /**
     * Get issuer
     *
     * @return boolean
     */
    public function getIsCheckingAccount()
    {
        return $this->isCheckingAccount;
    }

    /**
     * Set bankName
     *
     * @param string $bankName
     *
     * @return $this
     */
    public function setBankName($bankName)
    {
        $this->bankName = $bankName;

        return $this;
    }

    /**
     * Get bankName
     *
     * @return string
     */
    public function getBankName()
    {
        return $this->bankName;
    }
}
