<?php

namespace Erp\PaymentBundle\Repository;

use \Doctrine\ORM\EntityRepository;
use Erp\PaymentBundle\Entity\PaySimpleCustomer;
use Erp\PaymentBundle\Entity\PaySimpleRecurringPayment;

/**
 * Class PaySimpleCustomerRepository
 *
 * @package Erp\PaymentBundle\Repository
 */
class PaySimpleCustomerRepository extends EntityRepository
{
    /**
     * @param PaySimpleCustomer $customer
     *
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getLastActiveRecurring(PaySimpleCustomer $customer)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('ps_rp')
            ->from('Erp\PaymentBundle\Entity\PaySimpleRecurringPayment', 'ps_rp')
            ->where('ps_rp.status = :status')
            ->andWhere('ps_rp.paySimpleCustomer = :customer')
            ->orderBy('ps_rp.createdDate', 'DESC')
            ->setMaxResults(1)
            ->setParameter('status', PaySimpleRecurringPayment::STATUS_ACTIVE)
            ->setParameter('customer', $customer)
        ;

        $result = $qb->getQuery()->getOneOrNullResult();

        return $result;
    }
}
