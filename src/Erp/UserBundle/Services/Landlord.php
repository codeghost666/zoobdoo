<?php
namespace Erp\UserBundle\Services;

use Erp\UserBundle\Entity\User;

class Landlord extends UserService
{
    /**
     * Get Landlord statuses
     *
     * @param bool $withDesc
     *
     * @return array
     */
    public function getStatuses($withDesc = false)
    {
        $statuses = [User::STATUS_ACTIVE, User::STATUS_PENDING, User::STATUS_NOT_CONFIRMED];
        if ($withDesc) {
            $statuses = [
                User::STATUS_ACTIVE        => 'Active (Paid, Confirmed by Admin)',
                User::STATUS_PENDING       => 'Pending (Not Paid)',
                User::STATUS_NOT_CONFIRMED =>
                    'CC/Bank authorized (Check transaction status at PaySimple and set Active)',
                User::STATUS_DISABLED      => 'Disabled',
                User::STATUS_REJECTED      => 'Rejected',
            ];
        }

        return $statuses;
    }

    /**
     * Check if Landlord`s property has active tenant
     *
     * @param User $landlord
     *
     * @return bool
     */
    public function checkIsLandlordHasTenants(User $landlord)
    {
        $result = false;
        $properties = $landlord->getProperties();
        /** @var $property \Erp\PropertyBundle\Entity\Property */
        foreach ($properties as $property) {
            if ($property->getTenantUser()) {
                $result = true;
            }
        }

        return $result;
    }
}
