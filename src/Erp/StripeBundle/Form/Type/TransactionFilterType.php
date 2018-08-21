<?php

namespace Erp\StripeBundle\Form\Type;

use Erp\PropertyBundle\Entity\Property;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Erp\UserBundle\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Doctrine\ORM\EntityRepository;

class TransactionFilterType extends AbstractFilterType
{
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * @inheritdoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('keyword',
                'text',
                [
                    'required' => false,
                    'label' => 'Search',
                    'attr' => [
                        'placeholder' => 'Customer, Amount, Type, Description... etc.',
                    ]
                ])
            ->add(
                'dateFrom',
                'date',
                [
                    'required' => false,
                    'label' => 'Date From',
                    'widget' => 'single_text',
                    'format' => 'MM/dd/yyyy',
                    'attr' => [
                        'placeholder' => 'Date From',
                    ]
                ]
            )
            ->add(
                'dateTo',
                'date',
                [
                    'required' => false,
                    'label' => 'Date To',
                    'widget' => 'single_text',
                    'format' => 'MM/dd/yyyy',
                    'attr' => [
                        'placeholder' => 'Date To',
                    ]
                ]
            );
    }

    /**
     * @inheritdoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'method' => 'GET',
            'csrf_protection' => false,
        ]);
    }
}
