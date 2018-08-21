<?php

namespace Erp\StripeBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Erp\PaymentBundle\Entity\StripeAccount;
use Erp\PaymentBundle\Entity\StripeCustomer;

class InvoiceRepository extends EntityRepository
{
    public function getGroupedInvoices(StripeAccount $stripeAccount = null, StripeCustomer $stripeCustomer = null, \DateTime $dateFrom = null, \DateTime $dateTo = null)
    {
        $qb = $this->createQueryBuilder('i');
        $qb->select('COUNT(i.id) as gAmount, MONTH(i.created) as gMonth, YEAR(i.created) as gYear, CONCAT(YEAR(i.created), \'-\', MONTH(i.created)) as interval');

        if ($stripeAccount) {
            $qb->where('i.account = :account')
                ->setParameter('account', $stripeAccount);
            if ($stripeCustomer) {
                $qb->orWhere('i.customer = :customer')
                    ->setParameter('customer', $stripeCustomer);
            }
        }

        if ($dateFrom) {
            if ($dateTo) {
                $qb->andWhere($qb->expr()->between('i.created', ':dateFrom', ':dateTo'))
                    ->setParameter('dateTo', $dateTo);
            } else {
                $qb->andWhere('i.created > :dateFrom');
            }
            $qb->setParameter('dateFrom', $dateFrom);
        }

        $qb->groupBy('gYear')
            ->addGroupBy('gMonth');

        return $qb->getQuery()->getResult();
    }

    public function getInvoices(StripeAccount $stripeAccount = null, StripeCustomer $stripeCustomer = null, \DateTime $dateFrom = null, \DateTime $dateTo = null)
    {
        $qb = $this->createQueryBuilder('i')
            ->orderBy('i.created', 'DESC');

        if ($stripeAccount) {
            $qb->where('i.account = :account')
                ->setParameter('account', $stripeAccount);
            if ($stripeCustomer) {
                $qb->orWhere('i.customer = :customer')
                    ->setParameter('customer', $stripeCustomer);
            }
        }

        if ($dateFrom) {
            if ($dateTo) {
                $qb->andWhere($qb->expr()->between('i.created', ':dateFrom', ':dateTo'))
                    ->setParameter('dateTo', $dateTo);
            } else {
                $qb->andWhere('i.created > :dateFrom');
            }
            $qb->setParameter('dateFrom', $dateFrom);
        }

        return $qb->getQuery()->getResult();
    }
}
