<?php

namespace Erp\AdminBundle\Admin;

use Erp\PropertyBundle\Entity\Property;
use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Doctrine\ORM\EntityRepository;
use Erp\CoreBundle\Form\ImageType;
use Erp\CoreBundle\Form\DocumentType;
use Erp\UserBundle\Entity\User;
use Sonata\AdminBundle\Route\RouteCollection;
use Knp\Menu\ItemInterface as MenuItemInterface;
use Sonata\AdminBundle\Admin\AdminInterface;
use Symfony\Component\Validator\Constraints\Count;
use Symfony\Component\Validator\Constraints\NotBlank;
use Doctrine\ORM\Query\Expr;

/**
 * Class Properties
 * @package Erp\AdminBundle\Admin
 */
class Properties extends Admin
{
    /**
     * @var string
     */
    protected $baseRoutePattern = 'properties-management/properties';

    /**
     * @var string
     */
    protected $baseRouteName = 'admin_erpuserbundle_properties';

    /**
     * @var array
     */
    protected $formOptions = [
        'validation_groups' => ['EditProperty']
    ];

    /**
     * @var FormMapper
     */
    protected $formMapper;

    /**
     * @param string $context
     *
     * @return mixed
     */
    public function createQuery($context = 'list')
    {
        $query = parent::createQuery($context);
        $query->andWhere($query->getRootAliases()[0] . '.status<>:status')
            ->join($query->getRootAliases()[0] . '.user', 'u', Expr\Join::WITH, 'u.enabled=:enabled')
            ->setParameter('status', Property::STATUS_DELETED)
            ->setParameter('enabled', true)
        ;

        return $query;
    }

    /**
     * @param RouteCollection $collection
     */
    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->add('removeTenant', $this->getRouterIdParameter() . '/remove-tenant');
        $collection->remove('export');
        $collection->remove('delete');
    }

    /**
     * Add link to sent invitation to complete profile
     *
     * @param MenuItemInterface $menu
     * @param string            $action
     * @param AdminInterface    $childAdmin
     *
     * @return $this|void
     */
    protected function configureTabMenu(MenuItemInterface $menu, $action, AdminInterface $childAdmin = null)
    {
        /** @var $property \Erp\PropertyBundle\Entity\Property */
        $property = $this->getSubject();
        if (!$property || $action !== 'edit') {
            return;
        }

        if ($property->getTenantUser()) {
            $textConfirm  = 'Are you sure you want to remove this Tenant from Property?'
                . ' All postponed and recurring payments of this Tenant will be cancelled.';
            $menu->addChild(
                'Remove Tenant from this Property',
                ['uri' => $this->generateObjectUrl('removeTenant', $property), 'class' => 'btn red-btn']
            )->setAttribute('onclick', 'if (!confirm("' . $textConfirm . '")) return false;');
        }

        return $this;
    }

    /**
     * @param mixed $object
     *
     * @return $this
     */
    public function prePersist($object)
    {
        return $this;
    }

    /**
     * @param mixed $object
     *
     * @return $this
     */
    public function preUpdate($object)
    {
        if ($object->getTenantUser() instanceof User) {
            $this->getConfigurationPool()->getContainer()->get('erp.users.user.service')->activateUser(
                $object->getTenantUser()
            );
        }

        return $this;
    }

    /**
     * When new administrator created
     *
     * @param mixed $object
     *
     * @return $this
     */
    public function postPersist($object)
    {
        if ($object instanceof Property) {
            $flashBag = $this->getRequest()->getSession()->getFlashBag();
            $flashBag->set('erp_sonata_flash_success', 'Property has been successfully created.');
        }

        return $this;
    }

    /**
     * When new administrator created
     *
     * @param mixed $object
     *
     * @return $this
     */
    public function postUpdate($object)
    {
        if ($object instanceof Property) {
            $this->getRequest()->getSession()->getFlashBag()->add(
                'erp_sonata_flash_success',
                'Property has been successfully updated.'
            );
        }
        return $this;
    }

    /**
     * Fields to be shown on filter forms
     *
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('name')
            ->add('tenantUser', null, [], null, ['expanded' => false, 'multiple' => true])
            ->add('user.email', null, ['label' => 'Manager Email']);
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
            ->add('city.name', null, ['label' => 'City'])
            ->add('city.stateCode', null, ['label' => 'State'])
            ->add('createdDate')
            ->add('updatedDate')
            ->add('_action', 'actions', ['actions' => ['edit' => []]]);
    }

    /**
     * Fields to be shown on create/edit forms
     *
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $this->formMapper = $formMapper;

        $this
            ->addName()
            ->addStateCode()
            ->addCity()
            ->addAddress()
            ->addZip()
            ->addUser()
            ->addTenantUser()
            ->addPrice()
            ->addSquareFootage()
            ->addStatus()
            ->addOfBeds()
            ->addOfBaths()
            ->addAmenities()
            ->addAboutProperties()
            ->addAdditionalDetails()
            ->addImages()
            ->addDocuments()
        ;
    }

    /**
     * @return $this
     */
    private function addName()
    {
        $this->formMapper->add('name', 'text', ['label' => 'Name']);

        return $this;
    }

    /**
     * @return $this
     */
    private function addAddress()
    {
        $this->formMapper->add('address', 'text', ['label' => 'Address']);

        return $this;
    }

    /**
     * @return $this
     */
    private function addStateCode()
    {
        $this->formMapper->add(
            'stateCode',
            'choice',
            [
                'choices'  => $this
                    ->getConfigurationPool()
                    ->getContainer()
                    ->get('erp.core.location')
                    ->getStates(),
                'placeholder' => 'Select state',
                'attr' => ['data-class' => 'states'],
                'label' => 'State'
            ]
        );

        return $this;
    }

    /**
     * @return $this
     */
    private function addCity()
    {
        $method = $this->getRequest()->getMethod();
        $parameters = $this->getRequest()->request->all();
        $cityId = count($parameters) ? $parameters[$this->getUniqid()]['city'] : null;

        $this->formMapper->add(
            'city',
            'entity',
            [
                'label' => 'City',
                'class' => 'Erp\CoreBundle\Entity\City',
                'attr' => ['data-class' => 'cities'],
                'query_builder' => function (EntityRepository $er) use ($method, $cityId) {
                    if ($method === 'POST') {
                        return $er->createQueryBuilder('c')
                            ->where('c.id = :cityId')
                            ->setParameter('cityId', $cityId);
                    } else {
                        $stateCode = ($this->id($this->getSubject()))
                            ? $this->getSubject()->getStateCode()
                            : null;

                        return $er->getCitiesByStateCodeQb($stateCode);
                    }
                }
            ]
        );

        return $this;
    }

    /**
     * @return $this
     */
    private function addZip()
    {
        $this->formMapper->add('zip', 'text', array('label' => 'Zip'));

        return $this;
    }

    /**
     * @return $this
     */
    private function addUser()
    {
        $readonly = (bool)$this->getSubject()->getUser();
        $this->formMapper->add(
            'user',
            'entity',
            [
                'label' => 'Manager',
                'class' => 'Erp\UserBundle\Entity\User',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->where('u.roles LIKE :roles')
                        ->setParameter('roles', '%"' . User::ROLE_MANAGER . '"%')
                        ->orderBy('u.username', 'ASC');
                },
                'disabled'     => $readonly
            ],
            [
                'admin_code' => 'sonata.page.admin.managers'
            ]
        );

        return $this;
    }

    /**
     * @return $this
     */
    private function addTenantUser()
    {
        $session = $this->getRequest()->getSession();
        $session->set('tenantUser', $this->getSubject()->getTenantUser());
        $readonly = (bool)$this->getSubject()->getTenantUser() || $this->getSubject()->getUser()->isReadOnlyUser();

        $this->formMapper->add(
            'tenantUser',
            'entity',
            [
                'label'         => 'Tenant',
                'class'         => 'Erp\UserBundle\Entity\User',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->where('u.roles LIKE :roles')
                        ->andWhere('u.enabled=:enabled')
                        ->orWhere('u.id=:userId')
                        ->setParameter('roles', '%"' . User::ROLE_TENANT . '"%')
                        ->setParameter('enabled', false)
                        ->setParameter('userId', $this->id($this->getSubject()->getTenantUser()))
                        ->orderBy('u.username', 'ASC');
                },
                'placeholder'   => 'No Tenant',
                'required'      => false,
                'disabled'     => $readonly
            ],
            [
                'admin_code' => 'sonata.page.admin.tenants'
            ]
        );

        return $this;
    }

    /**
     * @return $this
     */
    private function addPrice()
    {
        $this->formMapper->add(
            'price',
            'money',
            [
                'label' => 'Price',
                'currency' => 'USD',
            ]
        );

        return $this;
    }

    /**
     * @return $this
     */
    private function addSquareFootage()
    {
        $this->formMapper->add('squareFootage', 'number', array('label' => 'Square footage'));

        return $this;
    }

    /**
     * @return $this
     */
    private function addStatus()
    {
        $this->formMapper->add(
            'status',
            'choice',
            [
                'label' => 'Status',
                'choices'  => [
                    Property::STATUS_DRAFT      => 'Draft (saved, not published)',
                    Property::STATUS_AVAILABLE  => 'Available (published on the website)',
                    Property::STATUS_RENTED     => 'Rented (not published)'
                ],
                'preferred_choices' => [Property::STATUS_DRAFT]
            ]
        );

        return $this;
    }

    /**
     * @return $this
     */
    private function addOfBeds()
    {
        $this->formMapper->add(
            'ofBeds',
            'choice',
            [
                'choices'  => $this
                    ->getConfigurationPool()
                    ->getContainer()
                    ->get('erp.property.service')
                    ->getListOfBeds(),
                'placeholder' => 'No beds',
                'label' => 'Of beds',
                'required' => false
            ]
        );

        return $this;
    }

    /**
     * @return $this
     */
    private function addOfBaths()
    {
        $this->formMapper->add(
            'ofBaths',
            'choice',
            [
                'choices'  => $this
                    ->getConfigurationPool()
                    ->getContainer()
                    ->get('erp.property.service')
                    ->getListOfBaths(),
                'placeholder' => 'No baths',
                'label' => 'Of baths',
                'required' => false
            ]
        );

        return $this;
    }

    /**
     * @return $this
     */
    private function addAmenities()
    {
        $this->formMapper->add(
            'amenities',
            'textarea',
            [
                'label' => 'Amenities',
                'required' => false
            ]
        );

        return $this;
    }

    /**
     * @return $this
     */
    private function addAboutProperties()
    {
        $this->formMapper->add(
            'aboutProperties',
            'textarea',
            [
                'label' => 'About properties',
                'required' => false
            ]
        );

        return $this;
    }

    /**
     * @return $this
     */
    private function addAdditionalDetails()
    {
        $this->formMapper->add(
            'additionalDetails',
            'textarea',
            [
                'label' => 'Additional details',
                'required' => false
            ]
        );

        return $this;
    }

    /**
     * @return $this
     */
    private function addImages()
    {
        $this->formMapper->add(
            'images',
            'collection',
            [
                'type' => new ImageType(),
                'required' => true,
                'allow_add'    => true,
                'allow_delete'  => true,
                'delete_empty'  => true,
                'by_reference' => false,
                'attr' => [
                    'nested_form' => true,
                    'nested_form_min' => 1,
                ],
                'label_attr' => [
                    'type' => 'images'
                ],
                'label' => 'Property pictures',
                'options' => [
                    'label' => 'New picture'
                ],
                'constraints' => [
                    new Count([
                        'min' => 1,
                        'groups' => ['EditProperty']
                    ])
                ]
            ]
        );

        return $this;
    }

    /**
     * @return $this
     */
    private function addDocuments()
    {
        $this->formMapper->add(
            'documents',
            'collection',
            [
                'type' => new DocumentType(),
                'required' => false,
                'allow_add'    => true,
                'allow_delete'  => true,
                'delete_empty'  => true,
                'by_reference' => false,
                'attr' => [
                    'nested_form' => true,
                    'nested_form_min' => 0,
                ],
                'label' => 'Property documents',
                'validation_groups' => ['EditProperty'],
                'cascade_validation' => true,
                'options' => [
                    'label' => 'New document'
                ],
            ]
        );

        return $this;
    }
}
