<?php

namespace Erp\PropertyBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Erp\UserBundle\Entity\User;
use Erp\PropertyBundle\Entity\Property;
use Doctrine\ORM\Query\Expr;

class PropertyRentHistoryRepository extends EntityRepository {

    public function getHistory(User $user, \DateTime $dateFrom = null, \DateTime $dateTo = null) {
        $statuses = [
            Property::STATUS_AVAILABLE,
            Property::STATUS_RENTED,
            Property::STATUS_DRAFT,
        ];
        $qb = $this->createQueryBuilder('prh')
                ->select('prh.createdAt', 'prh.status', 'prh.id', 'CONCAT(YEAR(prh.createdAt),\'-\',MONTH(prh.createdAt)) as date')
                ->join('prh.property', 'p')
                ->where('p.user = :user')
                ->andWhere('prh.status IN (:statuses)')
                ->setParameter('statuses', $statuses)
                ->setParameter('user', $user)
                ->addGroupBy('p')
                ->addGroupBy('prh.status')
                ->addGroupBy('date');

        if ($dateFrom) {
            if ($dateTo) {
                $qb->andWhere($qb->expr()->between('prh.createdAt', ':dateFrom', ':dateTo'));
                $qb->setParameter('dateTo', $dateTo);
            } else {
                $qb->andWhere('prh.createdAt > :dateFrom');
            }
            $qb->setParameter('dateFrom', $dateFrom);
        }

        return $qb->getQuery()->getResult();
    }

}
