<?php

namespace Erp\CoreBundle\Formatter;

use Erp\StripeBundle\Entity\Transaction;

class InternalTypeFormatter {

    public function format($value) {
        if (null === $value || '' === $value) {
            return $value;
        }

        $result = '';
        switch ($value) {
            case Transaction::INTERNAL_TYPE_CHARGE:
                $result = "Charge";
                break;
            case Transaction::INTERNAL_TYPE_RENT_PAYMENT:
                $result = "Rent payment";
                break;
            case Transaction::INTERNAL_TYPE_LATE_RENT_PAYMENT:
                $result = "Late rent payment";
                break;
            case Transaction::INTERNAL_TYPE_TENANT_SCREENING:
                $result = "Tenant Screening";
                break;
            case Transaction::INTERNAL_TYPE_ANNUAL_SERVICE_FEE:
                $result = "Annual Service Fee";
                break;
        }

        return $result;
    }

}
