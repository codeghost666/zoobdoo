<?php

namespace Erp\AdminBundle\Admin;

use Sonata\AdminBundle\Admin\Admin as BaseAdmin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Doctrine\ORM\EntityRepository;
use Sonata\AdminBundle\Route\RouteCollection;

class PaySimpleHistory extends BaseAdmin
{
    protected $baseRouteName = 'admin_erppaymentbundle_ps_history';

    protected $baseRoutePattern = 'pay-simple/transactions';

    /**
     * Fields to be shown on lists
     *
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $userAttr = [
            'label'         => 'Tenant',
            'class'         => 'Erp\UserBundle\Entity\User',
            'query_builder' => function (EntityRepository $er) {
                return $er->createQueryBuilder('u')
                    ->select()
                    ->orderBy('u.lastName', 'ASC');
            },
            'property'      => 'getFullNameWithEmail',
            'required'      => true,
            'admin_code'    => 'sonata.page.admin.tenants',
        ];

        $propAttr = [
            'label'         => 'Property',
            'class'         => 'Erp\PropertyBundle\Entity\Property',
            'query_builder' => function (EntityRepository $er) {
                return $er->createQueryBuilder('p')
                    ->select()
                    ->orderBy('p.name', 'ASC');
            },
            'property'      => 'getName',
            'required'      => true,
            'admin_code'    => 'sonata.page.admin.properties',
        ];

        $listMapper->add('user', 'entity', $userAttr)
            ->add('property', 'entity', $propAttr)
            ->add('amount')
            ->add('transferDate')
            ->add('notes')
            ->add('createdDate')
            ->add('_action', 'actions', ['actions' => ['edit' => []]]);
    }

    /**
     * Fields to be shown on create/edit forms
     *
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper->add(
            'transferDate',
            'sonata_type_datetime_picker',
            [
                'dp_side_by_side' => true,
                'dp_use_current'  => true,
                'dp_use_seconds'  => false,
                'dp_default_date' => 'now',
                'datepicker_use_button' => false,
                'required' => false,
            ]
        )->add('notes', null, ['label' => 'Comments']);
    }

    /**
     * Fields to be shown on filter forms
     *
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $userOptions = [
            'expanded' => false,
            'multiple' => true,
        ];

        $datagridMapper->add('user.email', null, ['label' => 'Tenant Email'])
            ->add('property', null, [], null, $userOptions)
            ->add('amount');
    }

    /**
     * Add custom route
     *
     * @param RouteCollection $collection
     */
    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->remove('export')->remove('delete')->remove('create');
    }
}
