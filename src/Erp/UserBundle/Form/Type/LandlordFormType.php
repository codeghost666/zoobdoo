<?php

namespace Erp\UserBundle\Form\Type;

use Erp\UserBundle\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class LandlordFormType extends AbstractType
{
    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstname', 'text', ['label' => 'First name', 'required' => true, 'attr' => ['class' => 'form-control', 'pattern'=> '.{2,255}'],
                'constraints' => [
                    new NotBlank(
                        [
                            'message' => 'Please enter landlord First name',
                            'groups' => ['LandlordDetails']
                        ]
                    ),
                    new Length(
                        [
                            'min' => 2,
                            'max' => 255,
                            'minMessage' => 'First name cannot be less than 2 chars',
                            'maxMessage' => 'First name cannot be longer than 255 chars',
                            'groups' => ['LandlordDetails']
                        ]
                    )
                ]
            ])
            ->add('lastname', 'text', ['label' => 'Last name', 'required' => true, 'attr' => ['class' => 'form-control', 'pattern'=> '.{2,255}'],
                'constraints' => [
                    new NotBlank(
                        [
                            'message' => 'Please enter landlord Last name',
                            'groups' => ['LandlordDetails']
                        ]
                    ),
                    new Length(
                        [
                            'min' => 2,
                            'max' => 255,
                            'minMessage' => 'Last name cannot be less than 2 chars',
                            'maxMessage' => 'Last name cannot be longer than 255 chars',
                            'groups' => ['LandlordDetails']
                        ]
                    )
                ]
            ])
            ->add('phone', 'text', ['required' => true, 'attr' => ['class' => 'form-control']])
            ->add('email', 'email', ['required' => true, 'attr' => ['class' => 'form-control'],
                'constraints' => [
                    new NotBlank(
                        [
                            'message' => 'Email cannot be empty.',
                            'groups' => ['LandlordDetails']
                        ]
                    ),
                    new Email(
                        [
                            'message' => 'Lanlord must have valid email.',
                            'groups' => ['LandlordDetails']
                        ]
                    )
                ]
            ])
            ->add('addressOne', 'text', ['label' => 'Address', 'required' => false, 'attr' => ['class' => 'form-control']])
            ->add('submit', 'submit', ['label' => 'Save', 'attr' => ['class' => 'btn edit-btn btn-space']]
            );
    }

    /**
     * @inheritdoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'validation_groups' => ['LandlordDetails']
        ]);
    }

    public function getName()
    {
        return 'erp_user_landlords_create';
    }
}
