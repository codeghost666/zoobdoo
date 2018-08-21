<?php

namespace Erp\AdminBundle\Admin;

use Ivory\CKEditorBundle\Form\Type\CKEditorType;
use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Route\RouteCollection;

/**
 * Class StaticPages
 *
 * @package Erp\AdminBundle\Admin
 */
class StaticPages extends Admin
{
    protected $baseRoutePattern = 'staticpages';

    protected $baseRouteName = 'admin_erpsitebundle_staticpages';

    protected $formOptions = [
        'validation_groups' => ['StaticPage']
    ];

    /**
     * @param RouteCollection $collection
     */
    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->remove('delete');
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
            ->add('slug', 'text', ['attr' => ['readonly' => 'readonly']])
            ->add('metaTitle', 'text', ['required' => false])
            ->add('metaDescription', 'textarea', ['required' => false])
            ->add('headerTitle', 'text', ['label' => 'Header title / Menu title'])
            ->add('title', 'text', ['required' => false])
            ->add('content', CkeditorType::class,  [
                'config' => [
                    'toolbar' => 'full'
                ],
                'required' => false
            ])
            ->add('inSubmenu', null, ['label' => 'Show submenu item'])
        ;

        if ($this->getSubject()->getTemplate() != 'features') {
            $formMapper->add('withSubmenu', null, ['label' => 'Show submenu block']);
        }
    }

    /**
     * Fields to be shown on lists
     *
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('title', 'text')
            ->add('code', 'text')
            ->add('slug', 'text')
            ->add('_action', 'actions', ['actions' => ['edit' => []]]);
    }
}
