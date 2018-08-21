<?php
namespace Erp\SiteBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Email;

class LandlordInviteFormType extends AbstractType
{
    /**
     * @var FormBuilderInterface
     */
    protected $formBuilder;

    /**
     * Construct method
     */
    public function __construct()
    {
        $this->validationGroup = 'LandlordInvite';
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array                                        $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->formBuilder = $builder;
        $this
            ->addLandlordFirstName()
            ->addLandlordLastName()
            ->addLandlordEmail()
            ->addTenantEmail()
            ->addMessage()
        ;

        $this->formBuilder->add(
            'submit',
            'submit',
            ['label' => 'Send', 'attr' => ['class' => 'btn red-btn']]
        );
    }

    /**
     * @return $this
     */
    public function addLandlordFirstName()
    {
        $this->formBuilder->add(
            'landlordFirstName',
            'text',
            [
                'label' => 'Property Manager First Name',
                'label_attr' => ['class' => 'control-label required-label'],
                'attr' => ['class' => 'form-control'],
                'required' => true,
                'mapped' => false,
                'constraints' => [
                    new NotBlank(
                        [
                            'message' => 'Please enter Property Manager First Name',
                            'groups'  => [$this->validationGroup],
                        ]
                    ),
                    new Length(
                        [
                            'min' => 2,
                            'max' => 255,
                            'groups'  => [$this->validationGroup],
                        ]
                    ),
                ]
            ]
        );

        return $this;
    }

    /**
     * @return $this
     */
    public function addLandlordLastName()
    {
        $this->formBuilder->add(
            'landlordLastName',
            'text',
            [
                'label' => 'Property Manager Last Name',
                'label_attr' => ['class' => 'control-label required-label'],
                'attr' => ['class' => 'form-control'],
                'required' => true,
                'mapped' => false,
                'constraints' => [
                    new NotBlank(
                        [
                            'message' => 'Please enter Property Manager Last Name',
                            'groups'  => [$this->validationGroup],
                        ]
                    ),
                    new Length(
                        [
                            'min' => 2,
                            'max' => 255,
                            'groups'  => [$this->validationGroup],
                        ]
                    ),
                ]
            ]
        );

        return $this;
    }

    /**
     * @return $this
     */
    public function addLandlordEmail()
    {
        $this->formBuilder->add(
            'landlordEmail',
            'email',
            [
                'label' => 'Property Manager Email',
                'label_attr' => ['class' => 'control-label required-label'],
                'attr' => ['class' => 'form-control contact-email full-width'],
                'required' => true,
                'mapped' => false,
                'constraints' => [
                    new NotBlank(
                        [
                            'message' => 'Please enter Property Manager Email',
                            'groups'  => [$this->validationGroup],
                        ]
                    ),
                    new Email(
                        [
                            'message' => 'This value is not a valid Email address.
                                                      Use following formats: example@address.com',
                            'groups'  => [$this->validationGroup],
                        ]
                    ),
                    new Length(
                        [
                            'min' => 6,
                            'max' => 255,
                            'groups'  => [$this->validationGroup],
                        ]
                    ),
                ],
            ]
        );

        return $this;
    }

    /**
     * @return $this
     */
    public function addTenantEmail()
    {
        $this->formBuilder->add(
            'tenantEmail',
            'email',
            [
                'label' => 'Tenant Email',
                'label_attr' => ['class' => 'control-label required-label'],
                'attr' => ['class' => 'form-control contact-email full-width'],
                'required' => true,
                'mapped' => false,
                'constraints' => [
                    new NotBlank(
                        [
                            'message' => 'Please enter Tenant Email',
                            'groups'  => [$this->validationGroup],
                        ]
                    ),
                    new Email(
                        [
                            'message' => 'This value is not a valid Email address.
                                                      Use following formats: example@address.com',
                            'groups'  => [$this->validationGroup],
                        ]
                    ),
                    new Length(
                        [
                            'min' => 6,
                            'max' => 255,
                            'groups'  => [$this->validationGroup],
                        ]
                    ),
                ],
            ]
        );

        return $this;
    }

    /**
     * @return $this
     */
    public function addMessage()
    {
        $this->formBuilder->add(
            'message',
            'textarea',
            [
                'label' => 'Message',
                'label_attr' => ['class' => 'control-label'],
                'attr' => ['class' => 'full-width form-control', 'rows' => 8],
                'required' => false,
                'mapped' => false,
                'constraints' => [
                    new Length(
                        [
                            'max' => 1000,
                            'groups'  => [$this->validationGroup],
                        ]
                    ),
                ],
            ]
        );

        return $this;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            [
                'validation_groups' => [$this->validationGroup]
            ]
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'erp_site_send_invite_to_landlord_form';
    }
}
