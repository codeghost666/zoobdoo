<?php

namespace Erp\PaymentBundle\PaySimple\Exeptions;

use Exception;

/**
 * Class PaySimpleModelException
 *
 * @package Erp\PaymentBundle\PaySimple\Exeptions
 */
class PaySimpleModelException extends Exception
{
    public function __construct()
    {
        parent::__construct('Wrong PaySimple Model usage');
    }
}
