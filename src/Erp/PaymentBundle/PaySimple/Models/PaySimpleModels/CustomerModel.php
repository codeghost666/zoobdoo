<?php

namespace Erp\PaymentBundle\PaySimple\Models\PaySimpleModels;

use Erp\PaymentBundle\Entity\PaySimpleCustomer;
use Erp\PaymentBundle\PaySimple\Models\PaySimpleModelInterface;

class CustomerModel implements PaySimpleModelInterface
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
    protected $middleName;

    /**
     * @var string
     */
    protected $bStreetAddress1;

    /**
     * @var string
     */
    protected $bStreetAddress2;

    /**
     * @var string
     */
    protected $bCity;

    /**
     * @var string
     */
    protected $bStateCode;

    /**
     * @var string
     */
    protected $bZipCode;

    /**
     * @var string
     */
    protected $bCountry;

    /**
     * @var string
     */
    protected $sStreetAddress1;

    /**
     * @var string
     */
    protected $sStreetAddress2;

    /**
     * @var string
     */
    protected $sCity;

    /**
     * @var string
     */
    protected $sStateCode;

    /**
     * @var string
     */
    protected $sZipCode;

    /**
     * @var string
     */
    protected $sCountry;

    /**
     * @var string
     */
    protected $company;

    /**
     * @var string
     */
    protected $customerAccount;

    /**
     * @var string
     */
    protected $phone;

    /**
     * @var string
     */
    protected $altPhone;

    /**
     * @var string
     */
    protected $mobilePhone;

    /**
     * @var string
     */
    protected $fax;

    /**
     * @var string
     */
    protected $email;

    /**
     * @var string
     */
    protected $altEmail;

    /**
     * @var string
     */
    protected $webSite;

    /**
     * @var string
     */
    protected $notes;

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
     * Set middleName
     *
     * @param string $middleName
     *
     * @return $this
     */
    public function setMiddleName($middleName)
    {
        $this->middleName = $middleName;

        return $this;
    }

    /**
     * Get middleName
     *
     * @return string
     */
    public function getMiddleName()
    {
        return $this->middleName;
    }

    /**
     * Set bStreetAddress1
     *
     * @param string $bStreetAddress1
     *
     * @return $this
     */
    public function setBStreetAddress1($bStreetAddress1)
    {
        $this->bStreetAddress1 = $bStreetAddress1;

        return $this;
    }

    /**
     * Get bStreetAddress1
     *
     * @return string
     */
    public function getBStreetAddress1()
    {
        return $this->bStreetAddress1;
    }

    /**
     * Set bStreetAddress2
     *
     * @param string $bStreetAddress2
     *
     * @return $this
     */
    public function setBStreetAddress2($bStreetAddress2)
    {
        $this->bStreetAddress2 = $bStreetAddress2;

        return $this;
    }

    /**
     * Get bStreetAddress2
     *
     * @return string
     */
    public function getBStreetAddress2()
    {
        return $this->bStreetAddress2;
    }

    /**
     * Set bCity
     *
     * @param string $bCity
     *
     * @return $this
     */
    public function setBCity($bCity)
    {
        $this->bCity = $bCity;

        return $this;
    }

    /**
     * Get bCity
     *
     * @return string
     */
    public function getBCity()
    {
        return $this->bCity;
    }

    /**
     * Set bStateCode
     *
     * @param string $bStateCode
     *
     * @return $this
     */
    public function setBStateCode($bStateCode)
    {
        $this->bStateCode = $bStateCode;

        return $this;
    }

    /**
     * Get bStateCode
     *
     * @return string
     */
    public function getBStateCode()
    {
        return $this->bStateCode;
    }

    /**
     * Set bZipCode
     *
     * @param string $bZipCode
     *
     * @return $this
     */
    public function setBZipCode($bZipCode)
    {
        $this->bZipCode = $bZipCode;

        return $this;
    }

    /**
     * Get bZipCode
     *
     * @return string
     */
    public function getBZipCode()
    {
        return $this->bZipCode;
    }

    /**
     * Set bCountry
     *
     * @param string $bCountry
     *
     * @return $this
     */
    public function setBCountry($bCountry)
    {
        $this->bCountry = $bCountry;

        return $this;
    }

    /**
     * Get bCountry
     *
     * @return string
     */
    public function getBCountry()
    {
        return $this->bCountry;
    }

    /**
     * Set sStreetAddress1
     *
     * @param string $sStreetAddress1
     *
     * @return $this
     */
    public function setSStreetAddress1($sStreetAddress1)
    {
        $this->sStreetAddress1 = $sStreetAddress1;

        return $this;
    }

    /**
     * Get sStreetAddress1
     *
     * @return string
     */
    public function getSStreetAddress1()
    {
        return $this->sStreetAddress1;
    }

    /**
     * Set sStreetAddress2
     *
     * @param string $sStreetAddress2
     *
     * @return $this
     */
    public function setSStreetAddress2($sStreetAddress2)
    {
        $this->sStreetAddress2 = $sStreetAddress2;

        return $this;
    }

    /**
     * Get sStreetAddress2
     *
     * @return string
     */
    public function getSStreetAddress2()
    {
        return $this->sStreetAddress2;
    }

    /**
     * Set sCity
     *
     * @param string $sCity
     *
     * @return $this
     */
    public function setSCity($sCity)
    {
        $this->sCity = $sCity;

        return $this;
    }

    /**
     * Get sCity
     *
     * @return string
     */
    public function getSCity()
    {
        return $this->sCity;
    }

    /**
     * Set sStateCode
     *
     * @param string $sStateCode
     *
     * @return $this
     */
    public function setSStateCode($sStateCode)
    {
        $this->sStateCode = $sStateCode;

        return $this;
    }

    /**
     * Get sStateCode
     *
     * @return string
     */
    public function getSStateCode()
    {
        return $this->sStateCode;
    }

    /**
     * Set sZipCode
     *
     * @param string $sZipCode
     *
     * @return $this
     */
    public function setSZipCode($sZipCode)
    {
        $this->sZipCode = $sZipCode;

        return $this;
    }

    /**
     * Get sZipCode
     *
     * @return string
     */
    public function getSZipCode()
    {
        return $this->sZipCode;
    }

    /**
     * Set sCountry
     *
     * @param string $sCountry
     *
     * @return $this
     */
    public function setSCountry($sCountry)
    {
        $this->sCountry = $sCountry;

        return $this;
    }

    /**
     * Get sCountry
     *
     * @return string
     */
    public function getSCountry()
    {
        return $this->sCountry;
    }

    /**
     * Set company
     *
     * @param string $company
     *
     * @return $this
     */
    public function setCompany($company)
    {
        $this->company = $company;

        return $this;
    }

    /**
     * Get company
     *
     * @return string
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * Set customerAccount
     *
     * @param string $customerAccount
     *
     * @return $this
     */
    public function setCustomerAccount($customerAccount)
    {
        $this->customerAccount = $customerAccount;

        return $this;
    }

    /**
     * Get customerAccount
     *
     * @return string
     */
    public function getCustomerAccount()
    {
        return $this->customerAccount;
    }

    /**
     * Set phone
     *
     * @param string $phone
     *
     * @return $this
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone
     *
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set altPhone
     *
     * @param string $altPhone
     *
     * @return $this
     */
    public function setAltPhone($altPhone)
    {
        $this->altPhone = $altPhone;

        return $this;
    }

    /**
     * Get altPhone
     *
     * @return string
     */
    public function getAltPhone()
    {
        return $this->altPhone;
    }

    /**
     * Set mobilePhone
     *
     * @param string $mobilePhone
     *
     * @return $this
     */
    public function setMobilePhone($mobilePhone)
    {
        $this->mobilePhone = $mobilePhone;

        return $this;
    }

    /**
     * Get mobilePhone
     *
     * @return string
     */
    public function getMobilePhone()
    {
        return $this->mobilePhone;
    }

    /**
     * Set fax
     *
     * @param string $fax
     *
     * @return $this
     */
    public function setFax($fax)
    {
        $this->fax = $fax;

        return $this;
    }

    /**
     * Get fax
     *
     * @return string
     */
    public function getFax()
    {
        return $this->fax;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return $this
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set altEmail
     *
     * @param string $altEmail
     *
     * @return $this
     */
    public function setAltEmail($altEmail)
    {
        $this->altEmail = $altEmail;

        return $this;
    }

    /**
     * Get altEmail
     *
     * @return string
     */
    public function getAltEmail()
    {
        return $this->altEmail;
    }

    /**
     * Set webSite
     *
     * @param string $webSite
     *
     * @return $this
     */
    public function setWebSite($webSite)
    {
        $this->webSite = $webSite;

        return $this;
    }

    /**
     * Get webSite
     *
     * @return string
     */
    public function getWebSite()
    {
        return $this->webSite;
    }

    /**
     * Set notes
     *
     * @param string $notes
     *
     * @return $this
     */
    public function setNotes($notes)
    {
        $this->notes = $notes;

        return $this;
    }

    /**
     * Get notes
     *
     * @return string
     */
    public function getNotes()
    {
        return $this->notes;
    }
}
