<?php

namespace Erp\AdminBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Route\RouteCollection;

/**
 * Class ContactPageRequests
 *
 * @package Erp\AdminBundle\Admin
 */
class ContactPageRequests extends Admin
{
    protected $baseRoutePattern = 'contactpagerequests';

    protected $baseRouteName = 'admin_erpsitebundle_contactpagerequests';

    protected $formOptions = [
        'validation_groups' => ['ContactPageRequest']
    ];

    /**
     * @param RouteCollection $collection
     */
    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->clearExcept(['list', 'delete', 'batch']);
    }

    /**
     * Fields to be shown on create/edit forms
     *
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
    }

    /**
     * Fields to be shown on lists
     *
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('name')
            ->add('email', 'text', ['template' => 'ErpAdminBundle:MediaAdmin:list_field_email.html.twig'])
            ->add('phone')
            ->add('subject')
            ->add('message')
            ->add('createdDate')
            ->add('_action', 'actions', ['actions' => ['delete' => []]])
        ;
    }
}
