<?php

namespace Erp\UserBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Erp\UserBundle\Entity\User;

/**
 * Class Message
 *
 * @package Erp\UserBundle\Repository
 */
class MessageRepository extends EntityRepository
{
    /**
     * Return messages for user
     *
     * @param User $user
     * @param User $toUser
     *
     * @return array
     */
    public function getMessages(User $user, User $toUser)
    {
        $qb = $this->_em->createQueryBuilder()
            ->select('m')
            ->from($this->_entityName, 'm')
            ->where('m.toUser IN (:users)')
            ->andWhere('m.fromUser IN (:users)')
            ->setParameter('users', [$user, $toUser])
            ->addOrderBy('m.createdDate', 'DESC')
        ;

        $result = $qb->getQuery()->getResult();

        return $result;
    }

    /**
     * Return count unread nessages
     *
     * @param User $user
     * @param User|null $filterByFromUser
     *
     * @return array
     */
    public function getCountUnreadMessages(User $user, User $fromUser = null)
    {
        $qb = $this->_em->createQueryBuilder()
            ->select('COUNT(m)')
            ->from($this->_entityName, 'm')
            ->where('m.toUser = :toUser')
            ->andWhere('m.isRead = :isRead')
            ->setParameter('toUser', $user)
            ->setParameter('isRead', 0)
        ;

        if ($fromUser) {
            $qb->andWhere('m.fromUser = :fromUser')->setParameter('fromUser', $fromUser);
        }

        $result = $qb->getQuery()->getSingleScalarResult();

        return $result;
    }

    /**
     * Return count messages for user
     *
     * @param User $fromUser
     * @param User $toUser
     *
     * @return int
     */
    public function getTotalMessagesByToUser(User $fromUser, User $toUser)
    {
        $qb = $this->_em->createQueryBuilder()
            ->select('COUNT(m)')
            ->from($this->_entityName, 'm')
            ->where('m.toUser IN (:users)')
            ->andWhere('m.fromUser IN (:users)')
            ->setParameter('users', [$fromUser, $toUser])
        ;

        $result = $qb->getQuery()->getSingleScalarResult();

        return $result;
    }
}
