<?php

namespace Erp\PropertyBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Erp\PropertyBundle\Entity\ScheduledRentPayment;

class ScheduledRentPaymentRepository extends EntityRepository
{
    public function getScheduledRecurringPayments()
    {
        $qb = $this->getScheduledQueryBuilder();
        $qb->select('srp')
            ->andWhere('srp.type = :type')
            ->setParameter('type', ScheduledRentPayment::TYPE_RECURRING);

        return $qb->getQuery()->getResult();
    }

    public function getScheduledSinglePayments()
    {
        $qb = $qb = $this->getScheduledQueryBuilder();
        $qb->select('srp')
            ->andWhere('srp.type = :type')
            ->andWhere('srp.status = :status')
            ->setParameter('type', ScheduledRentPayment::TYPE_SINGLE)
            ->setParameter('status', ScheduledRentPayment::STATUS_PENDING);

        return $qb->getQuery()->getResult();
    }

    public function getEndingScheduledRentPayments()
    {
        $date = (new \DateTime())->setTime(0, 0)->modify('-1 day');
        $qb = $this->createQueryBuilder('srp');

        return $qb->select('srp')
            ->where('srp.endAt = :date')
            ->setParameter('date', $date);
    }

    private function getScheduledQueryBuilder()
    {
        $date = (new \DateTime())->setTime(0, 0)->modify('-1 day');
        $qb = $this->createQueryBuilder('srp');

        return $qb->select('srp')
            ->where('srp.nextPaymentAt = :date')
            ->setParameter('date', $date);
    }
}
