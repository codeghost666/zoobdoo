<?php

namespace Erp\CoreBundle\Formatter;

class TransactionStatusFormatter
{
    public function format($value)
    {
        if (null === $value || '' === $value) {
            return $value;
        }

        $result = '';
        switch ($value) {
            case "succeeded":
                $result = "Cleared";
                break;
            case "pending":
                $result = "Pending";
                break;
            case "failed":
                $result = "Failed";
                break;
       }

        return $result;
    }
}
