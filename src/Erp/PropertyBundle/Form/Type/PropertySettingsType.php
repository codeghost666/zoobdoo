<?php

namespace Erp\PropertyBundle\Form\Type;

use Erp\PropertyBundle\Entity\PropertySettings;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PropertySettingsType extends AbstractType {

    /**
     * @inheritdoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $months = array_combine(range(1, 31), range(1, 31));
        $builder
                ->add(
                        'dayUntilDue', 'choice', [
                    'label' => 'Rent Due Date',
                    'label_attr' => ['class' => 'control-label'],
                    'attr' => ['class' => 'form-control col-xs-4'],
                    'choices' => $months,
                    'choices_as_values' => true,
                        ]
                )
                ->add(
                        'paymentAmount', 'money', [
                    'currency' => false,
                    'label' => 'Rent Amount',
                    'label_attr' => ['class' => 'control-label'],
                    'attr' => ['class' => 'form-control col-xs-4'],
                        ]
                )
                ->add(
                        'allowPartialPayments', 'checkbox', [
                    'label' => 'Restrict Partial Payments',
                    'required' => false,
                        ]
                )
                ->add(
                        'allowCreditCardPayments', 'checkbox', [
                    'label' => 'Allow Credit Card Payments',
                    'required' => false,
                        ]
                )
                ->add(
                        'allowAutoDraft', 'checkbox', [
                    'label' => 'Set auto-draft from tenant account?',
                    'required' => false,
                        ]
        );
    }

    /**
     * @inheritdoc
     */
    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults([
            'data_class' => PropertySettings::class,
            'csrf_protection' => false,
        ]);
    }

    /**
     * @inheritdoc
     */
    public function getName() {
        return 'erp_property_payment_settings';
    }

}
