<?php
namespace Erp\PropertyBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Class ESignFormType
 *
 * @package Erp\PropertyBundle\Form\Type
 */
class ESignFormType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'email',
            'email',
            [
                'attr'        => ['class' => 'form-control contact-email'],
                'label'       => 'Your Email',
                'label_attr'  => ['class' => 'control-label required-label'],
                'required'    => true,
                'mapped'      => false,
                'constraints' => [
                    new NotBlank(['groups' => ['ESign']]),
                    new Email(
                        [
                            'message' => 'This value is not a valid Email address
                                          Use following formats: example@address.com',
                            'groups'  => ['ESign'],
                        ]
                    ),
                ],
            ]
        );

        $builder->add(
            'submit',
            'submit',
            ['label' => 'Submit', 'attr' => ['class' => 'btn red-btn']]
        );
    }

    /**
     * Form default options
     *
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            [
                'validation_groups' => ['ESign']
            ]
        );
    }

    /**
     * Form name
     *
     * @return string
     */
    public function getName()
    {
        return 'erp_esign_form';
    }
}
