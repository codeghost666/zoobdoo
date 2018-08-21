<?php

namespace Erp\PaymentBundle\PaySimple\Managers;

use Erp\PaymentBundle\PaySimple\Exeptions\PaySimpleManagerException;
use Erp\PaymentBundle\PaySimple\Managers\PaySimpleManagers\PaySimpleCustomerManager;
use Erp\PaymentBundle\PaySimple\Managers\PaySimpleManagers\PaySimplePaymentManager;
use Erp\PaymentBundle\PaySimple\Managers\PaySimpleManagers\PaySimpleRecurringManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

class PaySimpleManagerFactory
{
    /**
     * Get PaySimple manager instance
     *
     * @param string             $type
     * @param ContainerInterface $container
     *
     * @return PaySimpleManagerInterface
     * @throws PaySimpleManagerException
     */
    public static function getManagerInstance(
        $type,
        ContainerInterface $container,
        $paySimplelogin = null,
        $paySimpleApiSecretKey = null
    ) {
        switch ($type) {
            case PaySimpleManagerInterface::TYPE_CUSTOMER:
                $manager = new PaySimpleCustomerManager($container, $paySimplelogin, $paySimpleApiSecretKey);
                break;
            case PaySimpleManagerInterface::TYPE_PAYMENT:
                $manager = new PaySimplePaymentManager($container, $paySimplelogin, $paySimpleApiSecretKey);
                break;
            case PaySimpleManagerInterface::TYPE_RECURRING:
                $manager = new PaySimpleRecurringManager($container, $paySimplelogin, $paySimpleApiSecretKey);
                break;
            default:
                $available = [
                    PaySimpleManagerInterface::TYPE_CUSTOMER,
                    PaySimpleManagerInterface::TYPE_PAYMENT,
                    PaySimpleManagerInterface::TYPE_RECURRING,
                ];
                throw new PaySimpleManagerException(
                    sprintf(
                        'PaySimple manager %s not found. Available managers: %s',
                        $type,
                        implode(', ', $available)
                    )
                );
                break;
        }

        return $manager;
    }
}
