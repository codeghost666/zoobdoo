<?php
/**
 * Created by Samuel Moses.
 * User: web
 * Date: 4/25/18
 * Time: 11:17 AM
 */

namespace Erp\WorkorderBundle\Repository;


use Doctrine\ORM\EntityRepository;

class WorkorderRepository Extends EntityRepository
{
    public function getWorkOrderQuery($userid = null) {
        $qb = $this->createQueryBuilder('a')->orderBy('a.createdDate', 'DESC');
        return $qb->getQuery();


        //$sql = "SELECT a.id, a.description, a.status, a.createdDate, a.serviceDate, a.serviceTime, b.name as contractor FROM ErpWorkorderBundle:Workorder AS a LEFT JOIN ErpVendorBundle:Vendor AS b ON a.contractor=b.id";

        //return $this->_em->createQuery($sql);
    }
}