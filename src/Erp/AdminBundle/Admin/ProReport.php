<?php

namespace Erp\AdminBundle\Admin;

use Sonata\AdminBundle\Admin\Admin as BaseAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Route\RouteCollection;

class ProReport extends BaseAdmin
{
    protected $baseRouteName = 'admin_erpuserbundle_proreport';

    protected $baseRoutePattern = 'ask-a-pro/report';

    /**
     * Default Datagrid values
     *
     * @var array
     */
    protected $datagridValues = [
        '_page'       => 1,
    ];

    /**
     * @param RouteCollection $collection
     */
    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->remove('export');
        $collection->remove('create');
    }

    /**
     * @param string $context
     *
     * @return mixed
     */
    public function createQuery($context = 'list')
    {
        $query = parent::createQuery($context);
        $alias = $query->getRootAlias()[0];
        $query->addOrderBy('MONTH('.$alias.'.approvedDate)', 'DESC');

        return $query;
    }

    /**
     * Fields to be shown on lists
     *
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper->add('proConsultant', null, ['label' => 'Consultant'])
            ->add('countUsers', null, ['label' => '# of Referrals'])
            ->add('approvedDate', 'date', ['label' => 'Month', 'widget' => 'single_text', 'format' => 'Y-F'])
            ->add('updatedDate');
    }

    /**
     * Fields to be shown on filter forms
     *
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('proConsultant')
            ->add('countUsers', null, ['label' => '# of Referrals']);
    }
}
