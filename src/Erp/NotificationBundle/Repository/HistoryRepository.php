<?php

namespace Erp\NotificationBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Erp\UserBundle\Entity\User;

class HistoryRepository extends EntityRepository
{
    public function getHistoryByUser(User $user)
    {
        return $this->createQueryBuilder('h')
            ->select('h')
            ->join('h.property', 'p')
            ->where('p.user = :user')
            ->setParameter('user', $user)
            ->orderBy('h.createdAt', 'DESC')
            ->getQuery()->getResult();
    }
}
