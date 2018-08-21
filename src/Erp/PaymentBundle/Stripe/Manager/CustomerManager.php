<?php

namespace Erp\PaymentBundle\Stripe\Manager;

use Stripe\Customer;
use Stripe\BankAccount;

class CustomerManager extends AbstractManager
{
    public function create($params, $options = null)
    {
        return $this->client->sendCustomerRequest('create', $params, $options);
    }

    public function retrieve($id, $options = null)
    {
        return $this->client->sendCustomerRequest('retrieve', $id, $options);
    }

    public function createBankAccount(Customer $customer, $params, $options = null)
    {
        $params = array_merge($params, ['object' => 'bank_account']);

        return $this->client->sendCustomerSourceRequest($customer, 'create', ['source' => $params], $options);
    }

    public function retrieveBankAccount(Customer $customer, $id, $options = null)
    {
        return $this->client->sendCustomerSourceRequest($customer, 'retrieve', $id, $options);
    }

    public function retrieveCreditCard(Customer $customer, $id, $options = null)
    {
        return $this->client->sendCustomerSourceRequest($customer, 'retrieve', $id, $options);
    }

    public function update(Customer $account, $params, $options = null)
    {
        return $this->client->sendUpdateRequest($account, $params, $options);
    }
}