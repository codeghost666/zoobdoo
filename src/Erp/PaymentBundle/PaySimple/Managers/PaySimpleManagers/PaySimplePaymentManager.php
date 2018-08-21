<?php

namespace Erp\PaymentBundle\PaySimple\Managers\PaySimpleManagers;

use Erp\PaymentBundle\PaySimple\Exceptions\PaySimpleManagerException;
use Erp\PaymentBundle\PaySimple\Managers\PaySimpleAbstarctManager;

/**
 * Class PaySimpleCustomerManager
 *
 * @package Erp\PaymentBundle\PaySimple\Managers
 */
class PaySimplePaymentManager extends PaySimpleAbstarctManager
{
    const URI_CREATE_CC = '/v4/account/creditcard';
    const URI_CREATE_BA = '/v4/account/ach';

    const URI_GET_PAYMENT_ACCOUNTS = '/v4/customer/';
    const URI_GET_DEFAULT_CC       = '/defaultcreditcard';
    const URI_GET_DEFAULT_BA       = '/defaultach';

    const URI_MAKE_PAYMENT = '/v4/payment';

    /**
     * @param string $method
     *
     * @return string
     * @throws PaySimpleManagerException
     */
    public function proccess($method = '')
    {
        switch ($method) {
            case self::METHOD_PAYMENT_CREATE_CC:
                // the same logic
            case self::METHOD_PAYMENT_CREATE_BA:
                $response = $this->createPaymentAccount($method);
                break;
            case self::METHOD_PAYMENT_GET_DEFAULT_CC:
                // the same logic
            case self::METHOD_PAYMENT_GET_DEFAULT_BA:
                $response = $this->getDefaultPaymentAccount($method);
                break;
            case self::METHOD_PAYMENT_MAKE:
                $response = $this->make();
                break;
            case self::METHOD_PAYMENT_SET_DEFAULT_PAYMENT_ACCOUNT:
                $response = $this->setDefaultPaymentAccount();
                break;
            default:
                $available = [
                    self::METHOD_PAYMENT_CREATE_CC,
                    self::METHOD_PAYMENT_CREATE_BA,
                    self::METHOD_PAYMENT_GET_DEFAULT_CC,
                    self::METHOD_PAYMENT_GET_DEFAULT_BA
                ];
                throw new PaySimpleManagerException(
                    sprintf(
                        'PaySimple payment method %s not found. Available methods are: %s',
                        $method,
                        implode(', ', $available)
                    )
                );
                break;
        }

        return $response;
    }

    /**
     * @var model CustomerModel
     *
     * @return array
     */
    protected function createPaymentAccount($type)
    {
        $params = $type === self::METHOD_PAYMENT_CREATE_CC ? $this->prepareCCData() : $this->prepareBAData();
        $url = $type === self::METHOD_PAYMENT_CREATE_CC ? self::URI_CREATE_CC : self::URI_CREATE_BA;
        $response = $this->curl->setPostParams(json_encode($params))->execute(self::$apiEndpoint . $url);
        $response = $this->proccessResponce($response);

        return $response;
    }

    /**
     * @var model CustomerModel
     *
     * @return array
     */
    protected function setDefaultPaymentAccount()
    {
        $customer = $this->model->getCustomer();
        $accountId = $customer->getPrimaryType() === self::CREDIT_CARD ? $customer->getCcId() : $customer->getBaId();
        $url = self::URI_GET_PAYMENT_ACCOUNTS . '/' . $customer->getCustomerId() . '/' . $accountId;

        $response = $this->curl->setPostParams(json_encode([]), 'PUT')->execute(self::$apiEndpoint . $url);
        $response = $this->proccessResponce($response);

        return $response;
    }

    /**
     * @var model CustomerModel
     *
     * @return array
     */
    protected function make()
    {
        $params = $this->prepareMakeData();
        $response = $this->curl->setPostParams(json_encode($params))->execute(
            self::$apiEndpoint . self::URI_MAKE_PAYMENT
        );
        $response = $this->proccessResponce($response);

        return $response;
    }

    /**
     * @var model CustomerModel
     *
     * @return array
     */
    protected function getDefaultPaymentAccount($type)
    {
        $urlLast = $type === self::METHOD_PAYMENT_GET_DEFAULT_CC ? self::URI_GET_DEFAULT_CC : self::URI_GET_DEFAULT_BA;
        $url = self::URI_GET_PAYMENT_ACCOUNTS . $this->model->getCustomer()->getCustomerId() . $urlLast;

        $response = $this->curl->execute(self::$apiEndpoint . $url);
        $response = $this->proccessResponce($response);

        return $response;
    }

    /**
     * Set params for creating new Credit Card Payment Account
     *
     * @return array
     */
    private function prepareCCData()
    {
        $params = [
            'CustomerId'       => $this->model->getCustomer()->getCustomerId(),
            'CreditCardNumber' => $this->model->getNumber(),
            'ExpirationDate'   => $this->model->getExpirationDate(),
            'Issuer'           => $this->model->getIssuer(),
            'IsDefault'        => true,
            'BillingZipCode'   => $this->model->getBillingZipCode()
        ];

        return $params;
    }

    /**
     * Set params for creating new Bank Account Payment Account
     *
     * @return array
     */
    private function prepareBAData()
    {
        $params = [
            'CustomerId'        => $this->model->getCustomer()->getCustomerId(),
            'RoutingNumber'     => $this->model->getRoutingNumber(),
            'AccountNumber'     => $this->model->getAccountNumber(),
            'BankName'          => $this->model->getBankName(),
            'IsCheckingAccount' => $this->model->getIsCheckingAccount() ? "true" : "false",
            'IsDefault'         => true
        ];

        return $params;
    }

    /**
     * Set params for make one Payment
     *
     * @return array
     */
    private function prepareMakeData()
    {
        $params = [
            'AccountId'      => $this->model->getAccountId(),
            'Amount'         => $this->model->getAmount() + $this->model->getAllowance(),
            'SendToCustomer' => true
        ];

        return $params;
    }
}
