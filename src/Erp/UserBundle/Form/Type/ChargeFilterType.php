<?php

namespace Erp\UserBundle\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Erp\StripeBundle\Form\Type\AbstractFilterType;

class ChargeFilterType extends AbstractFilterType {

    /**
     * @inheritdoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('dateFrom', 'date', array(
                    'required' => false,
                    'label' => 'Date From',
                    'widget' => 'single_text',
                    'format' => 'MM/dd/yyyy',
                    'attr' => array(
                        'placeholder' => 'Date From',
                    )
                ))
                ->add('dateTo', 'date', array(
                    'required' => false,
                    'label' => 'Date To',
                    'widget' => 'single_text',
                    'format' => 'MM/dd/yyyy',
                    'attr' => array(
                        'placeholder' => 'Date To',
                    )
                ))
        ;
    }

    /**
     * @inheritdoc
     */
    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults([
            'method' => 'GET',
            'csrf_protection' => false,
        ]);
    }

}
