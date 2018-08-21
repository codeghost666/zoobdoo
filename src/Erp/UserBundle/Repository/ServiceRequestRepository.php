<?php

namespace Erp\UserBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Erp\UserBundle\Entity\User;

/**
 * Class ServiceRequest
 *
 * @package Erp\UserBundle\Repository
 */
class ServiceRequestRepository extends EntityRepository
{
    /**
     * Return ServiceRequests
     *
     * @param User $user
     * @param User $toUser
     *
     * @return array
     */
    public function getServiceRequests(User $user, User $toUser)
    {
        $qb = $this->_em->createQueryBuilder()
            ->select('sr')
            ->from($this->_entityName, 'sr')
            ->where('sr.toUser IN (:users)')
            ->andWhere('sr.fromUser IN (:users)')
            ->setParameter('users', [$user, $toUser])
            ->addOrderBy('sr.createdDate', 'DESC')
        ;

        $result = $qb->getQuery()->getResult();

        return $result;
    }

    /**
     * Return tenants by manager and count messages
     *
     * @param User $user
     *
     * @return object
     */
    public function getTenantsByManager(User $user)
    {
        $qb = $this->_em->createQueryBuilder()
            ->select('sr, COUNT(sr) as totalServiceRequests')
            ->from($this->_entityName, 'sr')
            ->where('sr.toUser = :user')
            ->setParameter('user', $user)
            ->addGroupBy('sr.fromUser')
        ;

        $result = $qb->getQuery()->getResult();

        return $result;
    }
}
