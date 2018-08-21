<?php

namespace Erp\StripeBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Erp\PaymentBundle\Entity\StripeAccount;
use Symfony\Component\Validator\Constraints as Assert;

class AccountVerificationType extends AbstractType
{

    /**
     * @inheritdoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'city',
                'text',
                [
                    'label' => 'City',
                ]
            )
            ->add(
                'line1',
                'text',
                [
                    'label' => 'Line 1',
                ]
            )
            ->add(
                'postalCode',
                'text',
                [
                    'label' => 'Postal Code',
                ]
            )
            ->add(
                'state',
                'text',
                [
                    'label' => 'State',
                ]
            )
            ->add(
                'businessName',
                'text',
                [
                    'label' => 'Business Name',
                ]
            )
            ->add(
                'businessTaxId',
                'text',
                [
                    'label' => 'Business Tax Id',
                ]
            )
            ->add('birthday', 'birthday', [
//                'widget' => 'text',
                'years' => range(1900, date('Y') - 14), //younger than 13 years old not allowed by STRIPE
                'invalid_message' => 'Please enter valid date',
                'validation_groups' => ['UserTermOfUse', 'ManagerRegister'],
                'constraints' => new Assert\Date([
                    'groups'=>['UserTermOfUse', 'ManagerRegister']])
                ])
            ->add(
                'firstName',
                'text',
                [
                    'label' => 'First Name',
                ]
            )
            ->add(
                'lastName',
                'text',
                [
                    'label' => 'Last Name',
                ]
            )
            ->add(
                'ssnLast4',
                'text',
                [
                    'label' => 'SSN Last 4 digits',
                    'constraints' => [
                        new Assert\Length([
                        'min' => 4,
                        'max' => 4,
                        'groups' => ['UserTermOfUse','ManagerRegister']]),
                        new Assert\NotBlank([
                            'groups' => ['UserTermOfUse','ManagerRegister']
                        ])
                        ],
                ]
            )
            ->add(
                'tosAcceptance',
                'checkbox',
                [
                    'label' => 'Term of use',
                    'mapped' => false
                ]
            );
    }

    /**
     * @inheritdoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => StripeAccount::class,
        ]);
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'erp_stripe_bank_account_verification';
    }
}