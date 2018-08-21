<?php

namespace Erp\PaymentBundle\PaySimple\Exceptions;

use Exception;

/**
 * Class PaySimpleManagerException
 *
 * @package Erp\PaymentBundle\PaySimple\Exeptions
 */
class PaySimpleManagerException extends Exception
{
    public function __construct($msg = '')
    {
        parent::__construct($msg ? $msg : 'Wrong PaySimple Managers usage');
    }
}
