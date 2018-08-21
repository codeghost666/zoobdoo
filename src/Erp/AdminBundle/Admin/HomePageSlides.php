<?php

namespace Erp\AdminBundle\Admin;

use Ivory\CKEditorBundle\Form\Type\CKEditorType;
use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Erp\CoreBundle\Form\ImageType;

/**
 * Class HomePageSlides
 *
 * @package Erp\AdminBundle\Admin
 */
class HomePageSlides extends Admin
{
    protected $baseRoutePattern = 'homepageslider';

    protected $baseRouteName = 'admin_erpsitebundle_homepage_slider';

    protected $formOptions = [
        'validation_groups' => ['HomePageSlider']
    ];

    /**
     * @param RouteCollection $collection
     */
    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->remove('delete');
        $collection->remove('export');
        $collection->remove('create');
    }

    /**
     * Fields to be shown on create/edit forms
     *
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('image', new ImageType(), ['label' => '1920x840px', 'required' => false])
            ->add('title', CkeditorType::class,  [
                'config' => [
                    'toolbar' => 'full'
                ],
                'required' => false
            ])
            ->add('text', CkeditorType::class,  [
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
        $listMapper
            ->add('id', 'text', ['label' => 'Slide #'])
            ->add('updatedDate')
            ->add('_action', 'actions', ['actions' => ['edit' => []]])
        ;
    }
}
