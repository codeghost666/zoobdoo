<?php

namespace Erp\StripeBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Erp\PaymentBundle\Entity\StripeAccount;
use Erp\PaymentBundle\Entity\StripeCustomer;

class TransactionRepository extends EntityRepository {

    public function getGroupedTransactions(StripeAccount $stripeAccount = null, StripeCustomer $stripeCustomer = null, \DateTime $dateFrom = null, \DateTime $dateTo = null) {
        $qb = $this->createQueryBuilder('t');
        $qb->select('SUM(t.amount) as gAmount, MONTH(t.created) as gMonth, YEAR(t.created) as gYear, CONCAT(YEAR(t.created), \'-\', MONTH(t.created)) as interval');

        if ($stripeAccount) {
            $qb->where('t.account = :account')
                    ->setParameter('account', $stripeAccount);
        }

        if ($stripeCustomer) {
            $qb->orWhere('t.customer = :customer')
                    ->setParameter('customer', $stripeCustomer);
        }

        if ($dateTo) {
            $dateTo->add(new \DateInterval('P1D')); // To include current date items also
        }
        if ($dateFrom) {
            if ($dateTo) {
                $qb->andWhere($qb->expr()->between('t.created', ':dateFrom', ':dateTo'))
                        ->setParameter('dateTo', $dateTo);
            } else {
                $qb->andWhere('t.created > :dateFrom');
            }
            $qb->setParameter('dateFrom', $dateFrom);
        } elseif ($dateTo) {
            $qb->andWhere('t.created < :dateTo')
                    ->setParameter('dateTo', $dateTo);
        }

        $qb->groupBy('gYear')
                ->addGroupBy('gMonth');

        return $qb->getQuery()->getResult();
    }

    public function getTransactionsQuery(StripeAccount $stripeAccount = null, StripeCustomer $stripeCustomer = null, \DateTime $dateFrom = null, \DateTime $dateTo = null, $type = null) {
        $qb = $this->createQueryBuilder('t')
                ->orderBy('t.created', 'DESC');

        if ($stripeAccount) {
            $qb->where('t.account = :account')
                    ->setParameter('account', $stripeAccount);
            if ($stripeCustomer) {
                $qb->orWhere('t.customer = :customer')
                        ->setParameter('customer', $stripeCustomer);
            }
        }

        if ($dateTo) {
            $dateTo->add(new \DateInterval('P1D')); // To include current date items also
        }
        if ($dateFrom) {
            if ($dateTo) {
                $qb->andWhere($qb->expr()->between('t.created', ':dateFrom', ':dateTo'))
                        ->setParameter('dateTo', $dateTo);
            } else {
                $qb->andWhere('t.created > :dateFrom');
            }
            $qb->setParameter('dateFrom', $dateFrom);
        } elseif ($dateTo) {
            $qb->andWhere('t.created < :dateTo')
                    ->setParameter('dateTo', $dateTo);
        }

        if ($type) {
            $qb->andWhere(
                    $qb->expr()->in(
                            't.type', $type
                    )
            );
        }

        return $qb->getQuery();
    }

    public function getTransactionsSearchQuery($stripeAccountId = null, $stripeCustomerId = null, \DateTime $dateFrom = null, \DateTime $dateTo = null, $type = null, $keywords = null) {
        $qb = $this->createQueryBuilder('t')
                ->orderBy('t.created', 'DESC');
        $qb->leftJoin('ErpPaymentBundle:StripeAccount', 'sa', 'WITH', 'sa.id = t.account');
        $qb->leftJoin('ErpPaymentBundle:StripeCustomer', 'sc', 'WITH', 'sc.id = t.customer');
        $qb->leftJoin('ErpUserBundle:User', 'u', 'WITH', 'sc.user = u.id');
        if ($stripeAccountId) {  //outgoing transaction (account -> customer)
            $qb
                    ->andWhere(
                            $qb->expr()->in(
                                    't.account', $stripeAccountId
                            )
            );
            if ($stripeCustomerId) {
                $qb
                        ->andWhere(
                                $qb->expr()->in(
                                        't.customer', $stripeCustomerId
                                )
                );
            }
        }

        if ($dateTo) {
            $dateTo->add(new \DateInterval('P1D')); // To include current date items also
        }
        if ($dateFrom) {
            if ($dateTo) {
                $qb->andWhere(
                                $qb->expr()->between('t.created', ':dateFrom', ':dateTo')
                        )
                        ->setParameter('dateTo', $dateTo);
            } else {
                $qb->andWhere('t.created > :dateFrom');
            }
            $qb->setParameter('dateFrom', $dateFrom);
        } elseif ($dateTo) {
            $qb->andWhere('t.created < :dateTo')
                    ->setParameter('dateTo', $dateTo);
        }


        if ($type) {
            $qb->andWhere(
                    $qb->expr()->in(
                            't.type', $type
                    )
            );
        }

        if ($keywords) {
            $words = explode(" ", $keywords);
            foreach ($words as $word) {
                $qb->andWhere(
                        $qb->expr()->orX(
                                $qb->expr()->like('u.firstName', ':word'), $qb->expr()->like('u.lastName', ':word'), $qb->expr()->like('t.metadata', ':word'), $qb->expr()->like('t.status', ':word'), $qb->expr()->like('t.internalType', ':word'), $qb->expr()->like('t.amount', ':word')
                        )
                )->setParameter('word', '%' . $word . '%');
            }
        }

        return $qb->getQuery();
    }

}
