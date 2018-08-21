<?php

namespace Erp\PropertyBundle\Form\Type;

use Erp\CoreBundle\Form\DocumentType;
use Erp\CoreBundle\Form\ImageType;
use Erp\PropertyBundle\Entity\Property;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Class EditPropertyFormType
 *
 * @package Erp\PropertyBundle\Form\Type
 */
class EditPropertyFormType extends AbstractType {

    /**
     * @var FormBuilderInterface
     */
    protected $formBuilder;

    /**
     * @var \Erp\CoreBundle\Services\LocationService
     */
    protected $locationService;

    /**
     * @var \Erp\PropertyBundle\Services\PropertyService
     */
    protected $propertyService;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container) {
        $this->locationService = $container->get('erp.core.location');
        $this->propertyService = $container->get('erp.property.service');
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $this->formBuilder = $builder;
        $this->addName()
                ->addStateAndCityElements()
                ->addAddress()
                ->addZip()
                ->addPrice()
                ->addOfBeds()
                ->addOfBaths()
                ->addStatus()
                ->addSquareFootage()
                ->addAmenities()
                ->addAboutProperties()
                ->addAdditionalDetails()
                ->addImages();

        $this->formBuilder->add(
                'submit', 'submit', ['label' => 'Submit', 'attr' => ['class' => 'btn red-btn']]
        );
    }

    /**
     * Form default options
     *
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(
                [
                    'data_class' => 'Erp\PropertyBundle\Entity\Property',
                    'validation_groups' => ['EditProperty']
                ]
        );
    }

    /**
     * Form name
     *
     * @return string
     */
    public function getName() {
        return 'erp_property_edit_form';
    }

    /**
     * @return $this
     */
    private function addName() {
        $this->formBuilder->add(
                'name', 'text', [
            'label' => 'Property',
            'attr' => ['class' => 'prop-details form-control'],
            'label_attr' => ['class' => 'control-label required-label'],
            'required' => true
                ]
        );

        return $this;
    }

    /**
     * Add city and state
     *
     * @return $this
     */
    private function addStateAndCityElements() {
        $this->formBuilder->addEventListener(FormEvents::PRE_SET_DATA, [$this, 'onPreSetData']);
        $this->formBuilder->addEventListener(FormEvents::PRE_SUBMIT, [$this, 'onPreSubmit']);

        return $this;
    }

    /**
     * @param FormEvent $event
     */
    public function onPreSetData(FormEvent $event) {
        $state = $event->getData()->getStateCode();
        $this->locationService->setCities($state);
        $this->addElements($event->getForm(), $state);
    }

    /**
     * @param FormEvent $event
     */
    public function onPreSubmit(FormEvent $event) {
        $state = $event->getData()['stateCode'];
        $this->locationService->setCities($state);
        $this->addElements($event->getForm(), $state);
    }

    /**
     * @param FormInterface $form
     * @param null          stateCode
     */
    private function addElements(FormInterface $form, $stateCode = null) {
        $form->add(
                'stateCode', 'choice', [
            'choices' => $this->locationService->getStates(),
            'attr' => ['class' => 'form-control select-control', 'data-class' => 'states'],
            'label' => 'State',
            'label_attr' => ['class' => 'control-label required-label'],
            'required' => false
                ]
        );

        $form->add(
                'city', 'entity', [
            'label' => 'City',
            'class' => 'Erp\CoreBundle\Entity\City',
            'attr' => ['class' => 'form-control select-control', 'data-class' => 'cities'],
            'label_attr' => ['class' => 'control-label required-label'],
            'required' => false,
            'query_builder' => function (EntityRepository $er) use ($stateCode) {
                return $er->getCitiesByStateCodeQb($stateCode);
            }
                ]
        );
    }

    /**
     * @return $this
     */
    private function addAddress() {
        $this->formBuilder->add(
                'address', 'text', [
            'attr' => ['class' => 'prop-details form-control'],
            'label' => 'Address',
            'label_attr' => ['class' => 'control-label required-label'],
            'required' => true
                ]
        );

        return $this;
    }

    /**
     * @return $this
     */
    private function addZip() {
        $this->formBuilder->add(
                'zip', 'text', [
            'attr' => ['class' => 'prop-details form-control', 'maxlength' => 5, 'placeholder' => '11111'],
            'label' => 'Zip',
            'label_attr' => ['class' => 'control-label required-label'],
            'required' => true
                ]
        );

        return $this;
    }

    /**
     * @return $this
     */
    private function addPrice() {
        $this->formBuilder->add(
                $this->formBuilder->create(
                                'settings', 'form', [
                            'data_class' => \Erp\PropertyBundle\Entity\PropertySettings::class,
                                ]
                        )
                        ->add(
                                'paymentAmount', 'money', [
                            'currency' => false,
                            'label' => 'Price',
                            'attr' => ['class' => 'prop-details prop-price form-control', 'placeholder' => '199'],
                            'label_attr' => ['class' => 'control-label required-label'],
                            'required' => true,
                            'constraints' => [
                                new NotBlank(
                                        [
                                    'message' => 'Please enter Price',
                                    'groups' => ['EditProperty']
                                        ]
                                )
                            ]
                                ]
                        )
        );

        return $this;
    }

    /**
     * @return $this
     */
    private function addOfBeds() {
        $this->formBuilder->add(
                'ofBeds', 'choice', [
            'label' => '# of Beds',
            'choices' => $this->propertyService->getListOfBeds(),
            'attr' => ['class' => 'form-control select-control'],
            'label_attr' => ['class' => 'control-label'],
            'required' => false
                ]
        );

        return $this;
    }

    /**
     * @return $this
     */
    private function addOfBaths() {
        $this->formBuilder->add(
                'ofBaths', 'choice', [
            'label' => '# of Baths',
            'choices' => $this->propertyService->getListOfBaths(),
            'attr' => ['class' => 'form-control select-control'],
            'label_attr' => ['class' => 'control-label'],
            'required' => false
                ]
        );

        return $this;
    }

    /**
     * @return $this
     */
    private function addStatus() {
        $attributes = ['class' => 'form-control select-control'];

        $choices = [
            Property::STATUS_DRAFT => 'Draft (saved, not published)',
            Property::STATUS_RENTED => 'Rented (not published)',
            Property::STATUS_AVAILABLE => 'Available (published on the website)',
        ];

        $this->formBuilder->add(
                'status', 'choice', [
            'label' => 'Status',
            'choices' => $choices,
            'attr' => $attributes,
            'label_attr' => ['class' => 'control-label required-label'],
            'required' => true,
                ]
        );

        return $this;
    }

    /**
     * @return $this
     */
    private function addSquareFootage() {
        $this->formBuilder->add(
                'squareFootage', 'number', [
            'attr' => ['class' => 'prop-details form-control', 'maxlength' => '7', 'placeholder' => '59'],
            'label' => 'Square Footage',
            'label_attr' => ['class' => 'control-label required-label'],
            'required' => true
                ]
        );

        return $this;
    }

    /**
     * @return $this
     */
    private function addAmenities() {
        $this->formBuilder->add(
                'amenities', 'textarea', [
            'label' => 'Amenities',
            'attr' => ['class' => 'prop-details form-control'],
            'label_attr' => ['class' => 'control-label'],
            'required' => false
                ]
        );

        return $this;
    }

    /**
     * @return $this
     */
    private function addAboutProperties() {
        $this->formBuilder->add(
                'aboutProperties', 'textarea', [
            'label' => 'About Property',
            'attr' => ['class' => 'prop-details form-control'],
            'label_attr' => ['class' => 'control-label'],
            'required' => false
                ]
        );

        return $this;
    }

    /**
     * @return $this
     */
    private function addAdditionalDetails() {
        $this->formBuilder->add(
                'additionalDetails', 'textarea', [
            'label' => 'Additional Details',
            'attr' => ['class' => 'prop-details form-control'],
            'label_attr' => ['class' => 'control-label'],
            'required' => false
                ]
        );

        return $this;
    }

    /**
     * @return $this
     */
    private function addImages() {
        $this->formBuilder->add(
                'images', 'collection', [
            'type' => new ImageType(),
            'allow_add' => true,
            'allow_delete' => true,
            'delete_empty' => true,
            'by_reference' => false,
            'label_attr' => [
                'type' => 'images'
            ],
            'label' => 'Images',
                ]
        );

        return $this;
    }

}
