<?php

namespace Erp\PaymentBundle\PaySimple\Models\PaySimpleModels;

use Erp\PaymentBundle\Entity\PaySimpleCustomer;
use Erp\PaymentBundle\PaySimple\Models\PaySimpleModelInterface;

/**
 * Class CreditCardModel
 *
 * @package Erp\PaymentBundle\Entity
 */
class CreditCardModel implements PaySimpleModelInterface
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
    protected $number;

    /**
     * @var string
     */
    protected $expMonths;

    /**
     * @var string
     */
    protected $expYear;

    /**
     * @var int
     */
    protected $issuer;

    /**
     * @var string
     */
    protected $billingZipCode;

    /**
     * @var string
     */
    protected $email;

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
     * Set number
     *
     * @param string $number
     *
     * @return $this
     */
    public function setNumber($number)
    {
        $this->number = $number;

        return $this;
    }

    /**
     * Get number
     *
     * @return string
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * Set expDate
     *
     * @param string $expMonths
     *
     * @return $this
     */
    public function setExpMonths($expMonths)
    {
        $this->expMonths = $expMonths;

        return $this;
    }

    /**
     * Get expDate
     *
     * @return string
     */
    public function getExpMonths()
    {
        return $this->expMonths;
    }

    /**
     * Set expYear
     *
     * @param string $expYear
     *
     * @return $this
     */
    public function setExpYear($expYear)
    {
        $this->expYear = $expYear;

        return $this;
    }

    /**
     * Get expYear
     *
     * @return string
     */
    public function getExpYear()
    {
        return $this->expYear;
    }

    /**
     * Set issuer
     *
     * @param int $issuer
     *
     * @return $this
     */
    public function setIssuer($issuer)
    {
        $this->issuer = $issuer;

        return $this;
    }

    /**
     * Get issuer
     *
     * @return int
     */
    public function getIssuer()
    {
        return $this->issuer;
    }

    /**
     * Set billingZipCode
     *
     * @param string $billingZipCode
     *
     * @return $this
     */
    public function setBillingZipCode($billingZipCode)
    {
        $this->billingZipCode = $billingZipCode;

        return $this;
    }

    /**
     * Get billingZipCode
     *
     * @return string
     */
    public function getBillingZipCode()
    {
        return $this->billingZipCode;
    }

    /**
     * @param string $email
     *
     * @return $this
     */
    public function setEmail($email = null)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
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
     * Get expiration date
     *
     * @return string
     */
    public function getExpirationDate()
    {
        $month = $this->getExpMonths() < 10 ? '0' . $this->getExpMonths() : $this->getExpMonths();

        return $month . '/20' . $this->getExpYear();
    }
}
