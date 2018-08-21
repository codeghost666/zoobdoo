<?php

namespace Erp\WorkorderBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class EditType extends AbstractType
{
    protected $manager;
    protected $vendor_list;

    public function __construct($manager = null, $vendor_list)
    {
        $this->manager = $manager;
        $this->vendor_list = $vendor_list;
    }
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'createdDate',
                'datetime',
                [
                    'required' => true,
                    'label' => 'Reported Date',
                    'widget' => 'single_text',
                    'format' => 'MM/dd/yyyy',
                    'label_attr' => ['class' => 'control-label required-label'],
                    'attr' => [
                        'placeholder' => 'MM/dd/yyyy',
                    ]
                ]
            )
            ->add(
                'status',
                'choice',
                [
                    'label' => 'Status',
                    'label_attr' => ['class' => 'control-label required-label'],
                    'attr' => [
                        'class' => 'form-control select-control',
                        'aria-labelledby' => "dLabel",
                        'data-class' => 'states'
                    ],
                    'choices' => ['Created', 'Processing', 'Complete'],
                    'multiple' => false,
                    'required' => true
                ]
            )
            ->add(
                'service',
                'text',
                [
                    'label' => 'Item being Serviced',
                    'required' => false,
                    'label_attr' => ['class' => 'control-label'],
                    'attr' => ['class' => 'form-control', 'pattern'=> '.{2,255}']
                ]
            )
            ->add(
                'currency',
                'choice',
                [
                    'label' => 'Currency',
                    'label_attr' => ['class' => 'control-label required-label'],
                    'attr' => [
                        'class' => 'form-control select-control',
                        'aria-labelledby' => "dLabel",
                        'data-class' => 'states'
                    ],
                    'choices' => ['USD'],
                    'multiple' => false,
                    'required' => true
                ]
            )
            ->add(
                'contractor',
                'choice',
                [
                    'label' => 'Customer',
                    'label_attr' => ['class' => 'control-label required-label'],
                    'attr' => [
                        'class' => 'form-control select-control',
                    ],
                    'choices' => $this->getVendorList(),
                    'required' => true
                ]
            )
            ->add(
                'manager', HiddenType::class, array('data'=>$this->manager)
            )
            ->add(
                'severity',
                'choice',
                [
                    'label' => 'Severity',
                    'label_attr' => ['class' => 'control-label required-label'],
                    'attr' => [
                        'class' => 'form-control select-control',
                        'aria-labelledby' => "dLabel",
                        'data-class' => 'states'
                    ],
                    'choices' => ['Minor', 'Moderate', 'Major', 'Severe'],
                    'multiple' => false,
                    'required' => true
                ]
            )
            ->add(
                'urgency',
                'choice',
                [
                    'label' => 'Urgency',
                    'label_attr' => ['class' => 'control-label required-label'],
                    'attr' => [
                        'class' => 'form-control select-control',
                        'aria-labelledby' => "dLabel",
                        'data-class' => 'states'
                    ],
                    'choices' => ['Normal', 'Urgent', 'Emergency'],
                    'multiple' => false,
                    'required' => true
                ]
            )
            ->add(
                'description',
                'textarea',
                [
                    'label' => 'Description',
                    'label_attr' => ['class' => 'control-label required-label'],
                    'attr' => [
                        'class' => 'full-width form-control',
                        'placeholder'=> 'Problem Description',
                        'maxlength' => 512,
                    ],
                    'required' => true,
                ]
            )
            ->add(
                'serviceDate',
                'date',
                [
                    'required' => true,
                    'label' => 'Service Date',
                    'widget' => 'single_text',
                    'format' => 'MM/dd/yyyy',
                    'label_attr' => ['class' => 'control-label required-label'],
                    'attr' => [
                        'placeholder' => 'MM/dd/yyyy',
                    ]
                ]
            )
            ->add(
                'serviceTime',
                'text',

                [
                    'label' => 'Service Time',
                    'required' => false,
                    'label_attr' => ['class' => 'control-label'],
                    'attr' => ['class' => 'form-control', 'placeholder' => 'hh:mm', 'pattern'=> '.{2,6}']
                ]
            )
            ->add('save', 'submit', ['label' => 'Submit', 'attr' => ['class' => 'btn red-btn', 'value' => 'Next']])
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Erp\WorkorderBundle\Entity\Edit'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'erp_workorderbundle_edit';
    }

    public function getVendorList() {
        return $this->vendor_list;
    }
}
