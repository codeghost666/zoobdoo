<?php

namespace Erp\NotificationBundle\Form\Type;

use Ivory\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Erp\NotificationBundle\Entity\Template;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Validator\Constraints as Assert;

class TemplateType extends AbstractType
{
    /**
     * @inheritdoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'type',
                TextType::class,
                [
                    'label' => 'Type',
                    'constraints' => [
                        new Assert\NotBlank(),
                    ]
                ]
            )
            ->add(
                'title',
                TextType::class,
                [
                    'label' => 'Title',
                    'constraints' => [
                        new Assert\NotBlank(),
                    ]
                ]
            )
            ->add(
                'description',
                CkeditorType::class,
                [
                    'label' => 'Description',
                    'constraints' => [
                        new Assert\NotBlank(),
                    ]
                ]
            )
            ->add(
                'submit',
                SubmitType::class,
                [
                    'label' => 'Save',
                ]
            );
    }

    /**
     * @inheritdoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Template::class,
        ]);
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'erp_notification_template';
    }
}
