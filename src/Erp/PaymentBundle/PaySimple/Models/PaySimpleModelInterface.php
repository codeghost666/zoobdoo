<?php

namespace Erp\PaymentBundle\PaySimple\Models;

use Erp\PaymentBundle\Entity\PaySimpleCustomer;

/**
 * Interface PaySimpleModelInterface
 *
 * @package Erp\PaymentBundle\PaySimple\Models
 */
interface PaySimpleModelInterface
{
    /**
     * @return string
     */
    public function getFirstName();

    /**
     * @return string
     */
    public function getLastName();

    /**
     * @param PaySimpleCustomer $customer
     *
     * @return mixed
     */
    public function setCustomer(PaySimpleCustomer $customer);

    /**
     * @return PaySimpleCustomer
     */
    public function getCustomer();
}
