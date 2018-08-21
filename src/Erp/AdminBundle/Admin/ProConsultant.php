<?php

namespace Erp\AdminBundle\Admin;

use Sonata\AdminBundle\Admin\Admin as BaseAdmin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Route\RouteCollection;

class ProConsultant extends BaseAdmin
{
    protected $baseRouteName = 'admin_erpuserbundle_proconsultants';

    protected $baseRoutePattern = 'ask-a-pro/consultants';

    /**
     * Default Datagrid values
     *
     * @var array
     */
    protected $datagridValues = [
        '_page'       => 1,
        '_sort_by'    => 'id',
        '_sort_order' => 'DESC'
    ];

    /**
     * @var array
     */
    protected $formOptions = [
        'validation_groups' => ['CreateConsultant']
    ];

    /**
     * Fields to be shown on lists
     *
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper->addIdentifier('email')
            ->add('firstName')
            ->add('lastName')
            ->add('phone')
            ->add('address')
            ->add('createdDate')
            ->add('updatedDate');
    }

    /**
     * Fields to be shown on create/edit forms
     *
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper->add('email')
            ->add('firstName')
            ->add('lastName')
            ->add('phone')
            ->add('address');
    }

    /**
     * Fields to be shown on filter forms
     *
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('email')
            ->add('firstName')
            ->add('lastName')
            ->add('phone')
            ->add('address');
    }

    /**
     * @param RouteCollection $collection
     */
    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->clearExcept(['list', 'edit', 'create']);
    }
}
