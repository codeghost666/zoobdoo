<?php

namespace Erp\PaymentBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class PaySimpleCreditCardFormType
 *
 * @package Erp\PaymentBundle\Form\Type
 */
class PaySimpleCreditCardAnonymousFormType extends AbstractType
{
    /**
     * @var FormBuilderInterface
     */
    protected $formBuilder;

    /**
     * Build form function
     *
     * @param FormBuilderInterface $builder the formBuilder
     * @param array                $options the options for this form
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->formBuilder = $builder;
        $this
            ->addEmail()
            ->addName('First')
            ->addName('Last')
            ->addPhone()
            ->addAddress()
            ->addCity()
            ->addStateCode()
            ->addZipCode()
            ->addIssuer()
            ->addNumber()
            ->addExpMonths()
            ->addExpYear();
    }

    /**
     * Form default options
     *
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(['data_class' => 'Erp\PaymentBundle\PaySimple\Models\PaySimpleModels\CreditCardModel']);
    }

    /**
     * Return unique name for this form
     *
     * @return string
     */
    public function getName()
    {
        return 'ps_cc_anonymous_form';
    }

    /**
     * @return $this
     */
    private function addPhone()
    {
        $constraints = [
            new NotBlank(['message' => 'Please enter your phone number']),
        ];

        $this->formBuilder->add(
            'phone',
            'text',
            [
                'label'       => 'Phone number',
                'label_attr'  => ['class' => 'control-label required-label'],
                'attr'        => ['class' => 'form-control', 'data-mask' => '999-999-9999'],
                'required'    => true,
                'constraints' => $constraints,
            ]
        );

        return $this;
    }

    /**
     * @return $this
     */
    private function addEmail()
    {
        $constraints = [
            new NotBlank(['message' => 'Please enter your email']),
        ];

        $this->formBuilder->add(
            'email',
            'email',
            [
                'label'       => 'Email',
                'label_attr'  => ['class' => 'control-label required-label'],
                'attr'        => ['class' => 'form-control'],
                'required'    => true,
                'constraints' => $constraints,
            ]
        );

        return $this;
    }

    /**
     * @return $this
     */
    private function addAddress()
    {
        $constraints = [
            new NotBlank(['message' => 'Please enter your address']),
        ];

        $this->formBuilder->add(
            'address',
            'text',
            [
                'label'       => 'Address',
                'label_attr'  => ['class' => 'control-label required-label'],
                'attr'        => ['class' => 'form-control'],
                'required'    => true,
                'constraints' => $constraints,
            ]
        );

        return $this;
    }

    /**
     * @return $this
     */
    private function addCity()
    {
        $constraints = [
            new NotBlank(['message' => 'Please enter your city']),
        ];

        $this->formBuilder->add(
            'city',
            'text',
            [
                'label'       => 'City',
                'label_attr'  => ['class' => 'control-label required-label'],
                'attr'        => ['class' => 'form-control'],
                'required'    => true,
                'constraints' => $constraints,
            ]
        );

        return $this;
    }

    /**
     * @return $this
     */
    private function addStateCode()
    {
        $constraints = [
            new NotBlank(['message' => 'Please enter your state code']),
        ];

        $this->formBuilder->add(
            'stateCode',
            'text',
            [
                'label'       => 'State Code',
                'label_attr'  => ['class' => 'control-label required-label'],
                'attr'        => ['class' => 'form-control'],
                'required'    => true,
                'constraints' => $constraints,
            ]
        );

        return $this;
    }

    /**
     * @return $this
     */
    private function addZipCode()
    {
        $constraints = [
            new NotBlank(['message' => 'Please enter your Zip code']),
        ];

        $this->formBuilder->add(
            'zipCode',
            'text',
            [
                'label'       => 'Zip Code',
                'label_attr'  => ['class' => 'control-label required-label'],
                'attr'        => ['class' => 'form-control', 'maxlength' => 5],
                'required'    => true,
                'constraints' => $constraints,
            ]
        );

        return $this;
    }

    /**
     * @param string $name
     *
     * @return $this
     */
    private function addName($name = 'First')
    {
        $lengthMessage = $name . ' Name should have minimum 2 characters and maximum 255 characters';
        $constraints = [
            new NotBlank(['message' => 'Please enter your ' . $name . ' Name']),
            new Length(
                [
                    'min'        => 2,
                    'max'        => 255,
                    'minMessage' => $lengthMessage,
                    'maxMessage' => $lengthMessage,
                ]
            ),
        ];

        $this->formBuilder->add(
            strtolower($name) . 'Name',
            'text',
            [
                'label'       => $name . ' Name',
                'label_attr'  => ['class' => 'control-label required-label'],
                'attr'        => ['class' => 'form-control'],
                'required'    => true,
                'constraints' => $constraints,
            ]
        );

        return $this;
    }

    /**
     * @return $this
     */
    private function addIssuer()
    {

        $this->formBuilder->add(
            'issuer',
            'choice',
            [
                'attr'       => ['class' => 'form-control select-control'],
                'label'      => 'Select Card Type',
                'label_attr' => ['class' => 'control-label required-label'],
                'required'   => true,
                'choices'    => [
                    '12' => 'Visa',
                    '13' => 'Master (MasterCard)',
                    '14' => 'Amex (American Express)',
                    '15' => 'Discover'
                ],
                'multiple'   => false,
                'expanded'   => false,
            ]
        );

        return $this;
    }

    /**
     * @return $this
     */
    private function addNumber()
    {
        $constraints = [
            new NotBlank(['message' => 'Please enter your Credit Card Number']),
            new Assert\CardScheme(
                [
                    'schemes' => ['VISA', 'AMEX', 'DISCOVER', 'MASTERCARD'],
                    'message' => 'Your Credit Card number is invalid'
                ]
            ),
        ];

        $this->formBuilder->add(
            'number',
            'text',
            [
                'attr'        => [
                    'class' => 'form-control',
                    'maxlength' => 16,
                    'pattern'   => '[0-9]{14,16}',
                    'title' => 'Credit Card Number must be 14, 15 or 16 digits in length'
                ],
                'label'       => 'Credit Card Number',
                'label_attr'  => ['class' => 'control-label required-label'],
                'required'    => true,
                'max_length'  => 20,
                'constraints' => $constraints,
            ]
        );

        return $this;
    }

    /**
     * @return $this
     */
    private function addExpMonths()
    {
        $this->formBuilder->add(
            'expMonths',
            'choice',
            [
                'required'   => true,
                'attr'       => ['class' => 'form-control select-control'],
                'label'      => 'Expiration Month',
                'label_attr' => ['class' => 'control-label required-label'],
                'choices'    => array_combine(range(1, 12), range(1, 12))
            ]
        );

        return $this;
    }

    /**
     * @return $this
     */
    private function addExpYear()
    {
        $this->formBuilder->add(
            'expYear',
            'choice',
            [
                'required'   => true,
                'attr'       => ['class' => 'form-control select-control'],
                'label'      => 'Expiration Year',
                'label_attr' => ['class' => 'control-label required-label'],
                'choices'    => array_combine(range(date('y'), date('y') + 12), range(date('Y'), date('Y') + 12))
            ]
        );

        return $this;
    }
}
