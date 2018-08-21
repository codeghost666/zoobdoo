<?php

namespace Erp\PaymentBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Erp\UserBundle\Entity\User;

/**
 * Class PaySimpleBankAccountFormType
 *
 * @package Erp\PaymentBundle\Form\Type
 */
class PaySimpleBankAccountFormType extends AbstractType
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
        $this->addName()
            ->addName('Last')
            ->addRoutingNumber()
            ->addAccountNumber()
            ->addBankName();

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
        $resolver->setDefaults(['data_class' => 'Erp\PaymentBundle\PaySimple\Models\PaySimpleModels\BankAccountModel']);
    }

    /**
     * Return unique name for this form
     *
     * @return string
     */
    public function getName()
    {
        return 'ps_bs_form';
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
            new NotBlank(['message' => 'Please enter your First Name']),
            new Length(
                [
                    'min'        => 2,
                    'max'        => 255,
                    'minMessage' => $name . ' Name should have minimum 2 characters and maximum 255 characters',
                    'maxMessage' => $name . ' Name should have minimum 2 characters and maximum 255 characters'
                ]
            )
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
    private function addRoutingNumber()
    {

        $constraints = [
            new NotBlank(['message' => 'Please enter your Bank Routing Number']),
            new Regex(
                [
                    'pattern' => '/^[0-9]{9}+$/',
                    'match'   => true,
                    'message' => 'Bank Routing Number should have 9 digits'
                ]
            ),
        ];
        $this->formBuilder->add(
            'routingNumber',
            'text',
            [
                'attr'        => ['class' => 'form-control'],
                'label'       => 'Routing Number',
                'label_attr'  => ['class' => 'control-label required-label'],
                'required'    => true,
                'max_length'  => 9,
                'constraints' => $constraints
            ]
        );

        return $this;
    }

    /**
     * @return $this
     */
    private function addAccountNumber()
    {

        $constraints = [
            new NotBlank(['message' => 'Please enter your Bank Account Number']),
            new Length(
                [
                    'min'        => 4,
                    'max'        => 100,
                    'minMessage' => 'Bank Account Number should have minimum 4 characters and maximum 100 digits',
                    'maxMessage' => 'Bank Account Number should have minimum 4 characters and maximum 100 digits'
                ]
            ),
            new Regex(
                [
                    'pattern' => '/^[0-9]+$/',
                    'match'   => true,
                    'message' => 'Bank Account Number must contains only digits'
                ]
            )
        ];
        $this->formBuilder->add(
            'accountNumber',
            'text',
            [
                'attr'        => ['class' => 'form-control'],
                'label'       => 'Account Number',
                'label_attr'  => ['class' => 'control-label required-label'],
                'required'    => true,
                'max_length'  => 100,
                'constraints' => $constraints
            ]
        );

        return $this;
    }

    /**
     * @return $this
     */
    private function addBankName()
    {

        $constraints = [
            new NotBlank(['message' => 'Please enter your Bank Name']),
            new Length(
                [
                    'min'        => 1,
                    'max'        => 100,
                    'minMessage' => 'Bank Name should have minimum 1 characters and maximum 100 digits',
                    'maxMessage' => 'Bank Name should have minimum 1 characters and maximum 100 digits'
                ]
            )
        ];
        $this->formBuilder->add(
            'bankName',
            'text',
            [
                'attr'        => ['class' => 'form-control'],
                'label'       => 'Bank Name',
                'label_attr'  => ['class' => 'control-label required-label'],
                'required'    => true,
                'max_length'  => 100,
                'constraints' => $constraints
            ]
        );

        return $this;
    }
}
