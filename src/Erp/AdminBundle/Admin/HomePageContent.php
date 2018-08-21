<?php

namespace Erp\AdminBundle\Admin;

use Ivory\CKEditorBundle\Form\Type\CKEditorType;
use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Erp\CoreBundle\Form\ImageType;

/**
 * Class HomePageContent
 *
 * @package Erp\AdminBundle\Admin
 */
class HomePageContent extends Admin
{
    protected $baseRoutePattern = 'homepagecontent';

    protected $baseRouteName = 'admin_erpsitebundle_homepage_content';

    protected $formOptions = [
        'validation_groups' => ['HomePageContent']
    ];

    /**
     * @param RouteCollection $collection
     */
    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->clearExcept(['list', 'show', 'edit']);
    }

    /**
     * Fields to be shown on create/edit forms
     *
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('serviceBody', CkeditorType::class, [
                'config' => [
                    'toolbar' => 'full'
                ],
                'required' => false
            ])
            ->add('featureBody', CkeditorType::class, [
                'config' => [
                    'toolbar' => 'full'
                ],
                'required' => false
            ])
        ;
    }

    /**
     * Fields to be shown on lists
     *
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper->add('_action', 'actions', ['actions' => ['edit' => []]]);
    }
}
