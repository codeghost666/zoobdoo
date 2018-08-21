<?php

namespace Erp\SiteBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ContactPageRequestFormType extends AbstractType
{
    /**
     * Construct method
     */
    public function __construct()
    {
        $this->validationGroup = 'ContactPageRequest';
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'name',
            'text',
            [
                'label_attr' => ['class' => 'control-label required-label'],
                'attr' => ['class' => 'form-control'],
                'required' => true
            ]
        );

        $builder->add(
            'email',
            'text',
            [
                'label_attr' => ['class' => 'control-label required-label'],
                'attr' => ['class' => 'form-control'],
                'required' => true
            ]
        );

        $builder->add(
            'phone',
            'text',
            [
                'label_attr' => ['class' => 'control-label'],
                'attr' => ['class' => 'form-control'],
                'required' => false
            ]
        );

        $builder->add(
            'subject',
            'text',
            [
                'label_attr' => ['class' => 'control-label required-label'],
                'attr' => ['class' => 'form-control'],
                'required' => true
            ]
        );

        $builder->add(
            'message',
            'textarea',
            [
                'label_attr' => ['class' => 'control-label required-label'],
                'attr' => ['class' => 'form-control'],
                'required' => true
            ]
        );

        $builder->add(
            'submit',
            'submit',
            ['label' => 'Send', 'attr' => ['class' => 'btn red-btn']]
        );
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => 'Erp\SiteBundle\Entity\ContactPageRequest',
                'validation_groups' => [$this->validationGroup]
            ]
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'erp_sitebundle_contactpagerequest';
    }
}
