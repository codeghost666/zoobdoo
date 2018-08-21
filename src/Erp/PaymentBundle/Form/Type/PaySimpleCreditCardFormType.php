<?php

namespace Erp\PaymentBundle\Form\Type;

use Erp\UserBundle\Entity\User;
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
class PaySimpleCreditCardFormType extends AbstractType
{
    /**
     * @var FormBuilderInterface
     */
    protected $formBuilder;

    /**
     * @var User
     */
    protected $user;

    /**
     * Construct method
     *
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Build form function
     *
     * @param FormBuilderInterface $builder the formBuilder
     * @param array                $options the options for this form
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->formBuilder = $builder;
        $this->addName('First')
            ->addName('Last')
            ->addIssuer()
            ->addNumber()
            ->addExpMonths()
            ->addExpYear();

        $this->formBuilder->add(
            'submit',
            'submit',
            ['label' => 'Submit', 'attr' => ['class' => 'btn submit-popup-btn']]
        );
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
        return 'ps_cc_form';
    }

    /**
     * @param string $name
     *
     * @return $this
     */
    private function addName($name = 'First')
    {
        $readOnly = (bool)$this->user->getPaySimpleCustomers()->first();
        $constraints = [
            new NotBlank(['message' => 'Please enter your ' . $name . ' Name']),
            new Length(
                [
                    'min'        => 2,
                    'max'        => 255,
                    'minMessage' => $name . ' Name should have minimum 2 characters and maximum 255 characters',
                    'maxMessage' => $name . ' Name should have minimum 2 characters and maximum 255 characters',
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
                'read_only'   => $readOnly
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
                'attr'        => ['class' => 'form-control', 'maxlength' => 16],
                'label'       => 'Credit Card Number',
                'label_attr'  => ['class' => 'control-label required-label'],
                'required'    => true,
                'max_length'  => 20,
                'constraints' => $constraints
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
