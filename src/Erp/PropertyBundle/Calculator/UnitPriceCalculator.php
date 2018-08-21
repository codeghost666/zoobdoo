<?php

namespace Erp\PropertyBundle\Calculator;

use Doctrine\Common\Persistence\ManagerRegistry;

class UnitPriceCalculator
{
    private $registry;

    public function __construct(ManagerRegistry $registry)
    {
        $this->registry = $registry;
    }

    public function calculate($quantity)
    {
        //TODO Store in db
        $settings = [
            [
                'min' => 1,
                'max' => 1,
                'amount' => 99,
            ],
            [
                'min' => 2,
                'max' => 29,
                'amount' => 20,
            ],
            [
                'min' => 30,
                'max' => 100000,
                'amount' => 15,
            ],
        ];

        $amount = 0;
        foreach ($settings as $setting) {
            for ($i = $setting['min']; $i <= $quantity; $i++) {
                $amount += $setting['amount'];

                if ($i == $setting['max']) {
                    break;
                }
            }
        }

        return $amount;
    }
}