<?php

namespace Erp\PaymentBundle\Stripe\Manager;

use Erp\PaymentBundle\Stripe\Client\Client;

abstract class AbstractManager
{
    /**
     * @var Client
     */
    protected $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }
}