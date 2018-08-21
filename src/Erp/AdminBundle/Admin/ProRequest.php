<?php

namespace Erp\AdminBundle\Admin;

use Sonata\AdminBundle\Admin\Admin as BaseAdmin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Erp\UserBundle\Entity\ProRequest as ProRequestEntity;
use Doctrine\ORM\EntityRepository;
use Erp\UserBundle\Entity\User;
use Sonata\AdminBundle\Route\RouteCollection;
use Knp\Menu\ItemInterface as MenuItemInterface;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Validator\ErrorElement;

class ProRequest extends BaseAdmin
{
    protected $baseRouteName = 'admin_erpuserbundle_prorequests';

    protected $baseRoutePattern = 'ask-a-pro/requests';

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
     * @var FormMapper
     */
    protected $formMapper;

    protected $formOptions = [
        'validation_groups' => ['ProRequestAdmin']
    ];

    /**
     * @var string
     */
    protected $isDisabled = false;

    /**
     * Validate save methods
     *
     * @param ErrorElement     $errorElement
     * @param ProRequestEntity $object
     */
    public function validate(ErrorElement $errorElement, $object)
    {
        $isApproved = $object->getStatus() == ProRequestEntity::STATUS_APPROVED;

        if ($isApproved && !$object->getProConsultant()) {
            $errorElement
                ->with('proConsultant')
                ->assertNotNull(['message' => 'Please select Consultant'])
                ->end();
        }

        if ($isApproved && !$object->getConsultantFee()) {
            $errorElement
                ->with('consultantFee')
                ->assertNotNull(['message' => 'Please enter Consultant Fee'])
                ->end();
        }
    }

    /**
     * Fields to be shown on lists
     *
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $this->formMapper = $listMapper;

        $this->formMapper->addIdentifier('subject');
        $this->addIsRefferal()
            ->addStatus()
            ->addProConsultant()
            ->addApprovedDate()
            ->addConsultantFee()
            ->addCreatedDate();
    }

    /**
     * Fields to be shown on create/edit forms
     *
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $proRequest = $this->getSubject();
        $this->isDisabled = false;
        if ($proRequest->getStatus() == ProRequestEntity::STATUS_PAYMENT_OK) {
            $this->isDisabled = true;
        }

        $this->formMapper = $formMapper;
        $this->addProConsultant()
            ->addSubject()
            ->addMessage()
            ->addServicingFee()
            ->addConsultantFee()
            ->addStatus(true);
    }

    /**
     * Fields to be shown on filter forms
     *
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('subject')
            ->add('user')
            ->add('status', null, [], 'choice', ['label' => 'Status', 'choices' => $this->getStatuses()])
            ->add('isRefferal')
            ->add('proConsultant')
            ->add('consultantFee');
    }

    /**
     * Add custom route
     *
     * @param RouteCollection $collection
     */
    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->add('chargeManager', $this->getRouterIdParameter().'/charge-manager');
        $collection->remove('export');
        $collection->remove('create');
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
        /** @var $proRequest \Erp\UserBundle\Entity\ProRequest */
        $proRequest = $this->getSubject();
        if (!$proRequest || $action !== 'edit') {
            return;
        }

        $isApprovedStatus = $proRequest->getStatus() == ProRequestEntity::STATUS_APPROVED;
        $isConsultant = $proRequest->getProConsultant();
        $isConsultantFee = $proRequest->getConsultantFee();

        if ($isApprovedStatus && $isConsultant && $isConsultantFee) {
            $menu->addChild(
                'Charge Manager',
                ['uri' => $this->generateObjectUrl('chargeManager', $proRequest), ['class' => 'btn red-btn']]
            );
        }

        return $this;
    }

    /**
     * @return $this
     */
    private function addProConsultant()
    {
        $attr = [
            'label'         => 'Consultant',
            'class'         => 'Erp\UserBundle\Entity\ProConsultant',
            'query_builder' => function (EntityRepository $er) {
                return $er->createQueryBuilder('pc')
                    ->select()
                    ->orderBy('pc.lastName', 'ASC');
            },
            'property'      => 'getFullNameWithEmail',
            'required'      => false
        ];
        !$this->isDisabled ?: $attr['attr']['disabled'] = 'disabled';
        $this->formMapper->add('proConsultant', 'entity', $attr, ['admin_code' => 'sonata.page.admin.prorequests']);

        return $this;
    }

    /**
     * Add subject field
     *
     * @return $this
     */
    private function addSubject()
    {
        $attr = ['label' => 'Subject', 'required' => true];
        !$this->isDisabled ? : $attr['attr']['disabled'] = 'disabled';
        $this->formMapper->add('subject', null, $attr);

        return $this;
    }

    /**
     * Add message field
     *
     * @return $this
     */
    private function addMessage()
    {
        $attr = ['label' => 'Message', 'required' => true];
        !$this->isDisabled ? : $attr['attr']['disabled'] = 'disabled';
        $this->formMapper->add('message', null, $attr);

        return $this;
    }

    /**
     * Add message field
     *
     * @return $this
     */
    private function addIsRefferal()
    {
        $attr = ['label' => 'Is Referral?'];
        $attr['attr']['read_only'] = 'readonly';
        $this->formMapper->add('isRefferal', null, $attr);

        return $this;
    }

    /**
     * Add field with statuses
     *
     * @param bool $isEdit
     *
     * @return $this
     */
    private function addStatus($isEdit = false)
    {
        $statuses = $this->getStatuses();
        if ($isEdit && !$this->isDisabled) {
            unset($statuses[ProRequestEntity::STATUS_PAYMENT_OK]);
            unset($statuses[ProRequestEntity::STATUS_PAYMENT_ERROR]);
        }
        $attr = ['label' => 'Status', 'choices' => $statuses];
        !$this->isDisabled ? : $attr['attr']['disabled'] = 'disabled';
        $this->formMapper->add('status', 'choice', $attr);

        return $this;
    }

    /**
     * Add message field
     *
     * @return $this
     */
    private function addApprovedDate()
    {
        $this->formMapper->add('approvedDate', null, ['label' => 'Charge Date']);

        return $this;
    }

    /**
     * Add message field
     *
     * @return $this
     */
    private function addConsultantFee()
    {
        $attr = ['label' => 'Consultant Fee'];
        !$this->isDisabled ? : $attr['attr']['disabled'] = 'disabled';
        $this->formMapper->add('consultantFee', null, $attr);

        return $this;
    }

    /**
     * Add message field
     *
     * @return $this
     */
    private function addServicingFee()
    {
        $attr = ['label' => 'Ask a Pro Fee'];
        !$this->isDisabled ? : $attr['attr']['disabled'] = 'disabled';
        $this->formMapper->add('servicingFee', null, $attr);

        return $this;
    }

    /**
     * Add message field
     *
     * @return $this
     */
    private function addCreatedDate()
    {
        $this->formMapper->add('createdDate', null, ['label' => 'Created Date']);

        return $this;
    }

    /**
     * Get request statuses
     *
     * @return array
     */
    private function getStatuses()
    {
        return [
            ProRequestEntity::STATUS_IN_PROCESS    => 'Waiting for Consultant Cost',
            ProRequestEntity::STATUS_APPROVED      => 'Approved by Manager (Ready to be charged)',
            ProRequestEntity::STATUS_PAYMENT_OK    => 'Payment Success',
            ProRequestEntity::STATUS_PAYMENT_ERROR => 'Payment Error',
            ProRequestEntity::STATUS_CANCELED      => 'Canceled',
        ];
    }
}
