<?php

namespace Erp\PaymentBundle\PaySimple\Managers\PaySimpleManagers;

use Erp\PaymentBundle\Entity\PaySimpleRecurringPayment;
use Erp\PaymentBundle\PaySimple\Exceptions\PaySimpleManagerException;
use Erp\PaymentBundle\PaySimple\Managers\PaySimpleAbstarctManager;
use Erp\PaymentBundle\PaySimple\Managers\PaySimpleManagerInterface;

/**
 * Class PaySimpleCustomerManager
 *
 * @package Erp\PaymentBundle\PaySimple\Managers
 */
class PaySimpleCustomerManager extends PaySimpleAbstarctManager
{
    const URI_CUSTOMER = '/v4/customer';

    /**
     * @param string $method
     *
     * @return string
     * @throws PaySimpleManagerException
     */
    public function proccess($method = '')
    {
        switch ($method) {
            case self::METHOD_CUSTOMER_GET_LIST_CUSTOMERS:
                $responce = $this->getListCustomers();
                break;
            case self::METHOD_CUSTOMER_CREATE:
                $responce = $this->createCustomer();
                break;
            case self::METHOD_CUSTOMER_GET_INFO:
                $responce = $this->getCustomerInfo();
                break;
            case self::METHOD_CUSTOMER_GET_ACTIVE_SCHEDULES:
                $responce = $this->getActiveCustomerSchedules();
                break;
            case self::METHOD_CUSTOMER_DELETE:
                $responce = $this->deleteCustomerRecords();
                break;
            default:
                $available = [
                    self::METHOD_CUSTOMER_GET_LIST_CUSTOMERS,
                    self::METHOD_CUSTOMER_CREATE,
                    self::METHOD_CUSTOMER_GET_INFO,
                    self::METHOD_CUSTOMER_GET_ACTIVE_SCHEDULES,
                ];
                throw new PaySimpleManagerException(
                    sprintf(
                        'PaySimple customer method %s not found. Available methods are: %s',
                        $method,
                        implode(', ', $available)
                    )
                );
                break;
        }

        return $responce;
    }

    /**
     * @return array|mixed
     */
    protected function getListCustomers()
    {
        $url = self::$apiEndpoint . self::URI_CUSTOMER . '?lite=1';

        $response = $this->curl->execute($url);
        $response = $this->proccessResponce($response);

        return $response;
    }

    /**
     * @var model CustomerModel
     *
     * @return array
     */
    protected function createCustomer()
    {
        $params = json_encode($this->prepareCreateData());
        $response = $this->curl->setPostParams($params)->execute(self::$apiEndpoint . self::URI_CUSTOMER);
        $response = $this->proccessResponce($response);

        return $response;
    }

    /**
     * @return array|mixed
     */
    protected function getCustomerInfo()
    {
        $url = self::$apiEndpoint . self::URI_CUSTOMER . '/' . $this->model->getCustomer()->getCustomerId();
        $response = $this->curl->execute($url);
        $response = $this->proccessResponce($response);

        return $response;
    }

    /**
     * @return array|mixed
     */
    protected function deleteCustomerRecords()
    {
        $url = self::$apiEndpoint . self::URI_CUSTOMER . '/' . $this->model->getCustomer()->getCustomerId();
        $response = $this->curl->setPostParams('', 'DELETE')->execute($url);
        $response = $this->proccessResponce($response);

        return $response;
    }

    /**
     * Get active customer schedules
     *
     * @return array
     */
    protected function getActiveCustomerSchedules()
    {
        $params = [
            'status' => PaySimpleRecurringPayment::STATUS_ACTIVE,
            'lite' => 'true',
        ];

        $url = self::$apiEndpoint
            . self::URI_CUSTOMER
            . '/' . $this->model->getCustomer()->getCustomerId()
            . '/paymentschedules'
            . '?' . http_build_query($params);

        $response = $this->curl->execute($url);
        $response = $this->proccessResponce($response);

        return $response;
    }

    /**
     * Set params for creating new customer
     *
     * @return array
     */
    private function prepareCreateData()
    {
        $params = [
            'FirstName'             => $this->model->getFirstName(),
            'LastName'              => $this->model->getLastName(),
            'MiddleName'            => $this->model->getMiddleName(),
            'BillingAddress'        => [
                'StreetAddress1' => $this->model->getBStreetAddress1(),
                'StreetAddress2' => $this->model->getBStreetAddress2(),
                'City'           => $this->model->getBCity(),
                'StateCode'      => $this->model->getBStateCode(),
                'ZipCode'        => $this->model->getBZipCode(),
                'Country'        => $this->model->getBCountry(),
            ],
            'CustomerAccount'       => $this->model->getCustomerAccount(),
            'ShippingSameAsBilling' => true,
            'Company'               => $this->model->getCompany(),
            'Phone'                 => $this->model->getPhone(),
            'AltPhone'              => $this->model->getAltPhone(),
            'MobilePhone'           => $this->model->getMobilePhone(),
            'Fax'                   => $this->model->getFax(),
            'Email'                 => $this->model->getEmail(),
            'AltEmail'              => $this->model->getAltEmail(),
            'Website'               => $this->model->getWebSite(),
            'Notes'                 => $this->model->getNotes()
        ];

        return $params;
    }
}
