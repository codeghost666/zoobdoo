<?php

namespace Erp\PaymentBundle\Form\Type;

use Erp\UserBundle\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class PaySimplePayRentFormType
 *
 * @package Erp\PaymentBundle\Form\Type
 */
class PaySimplePayRentFormType extends AbstractType
{
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
        $startDatePattern = '/^[\d]{2}\/[\d]{2}\/[\d]{4}$/';
        $isDisabled = !(bool)$this->user->getPaySimpleCustomers()->first();

        $builder->add(
            'amount',
            'number',
            [
                'label'       => false,
                'attr'        => ['class' => 'form-control subject', 'placeholder' => 'Amount $'],
                'required'    => true,
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Amount should not be empty']),
                    new Assert\Length(
                        [
                            'min'        => 1,
                            'max'        => 4,
                            'minMessage' => ' Enter the amount in the range from $1 to $9999',
                            'maxMessage' => ' Enter the amount in the range from $1 to $9999'
                        ]
                    ),
                    new Assert\Range(
                        [
                            'min'        => 0.01,
                            'max'        => 1000000,
                            'minMessage' => ' Amount should have minimum 0.01$ and maximum $1,000,000',
                            'maxMessage' => ' Amount should have minimum 0.01$ and maximum $1,000,000'
                        ]
                    )
                ],
            ]
        )->add(
            'startDate',
            'text',
            [
                'label'       => false,
                'attr'        => ['class' => 'form-control subject date', 'placeholder' => 'Payment Date'],
                'required'    => true,
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Payment Date should not be empty']),
                    new Assert\Date(['message' => ' Is not a valid date format']),
                    new Assert\GreaterThanOrEqual(
                        ['value' => 'today', 'message' => 'Please select today\'s or future date.']
                    ),
                ],
            ]
        )->add(
            'isRecurring',
            'checkbox',
            [
                'required'   => false,
                'label'      => 'Make this a recurring payment',
                'label_attr' => ['class' => 'control-label'],
                'disabled'   => $isDisabled
            ]
        )->add(
            'submit',
            'submit',
            ['label' => 'Make Payment', 'attr' => ['class' => 'btn red-btn'], 'disabled' => $isDisabled]
        );

        $builder->addEventListener(
            FormEvents::PRE_SUBMIT,
            function (FormEvent $event) use ($startDatePattern) {
                $data = $event->getData();
                if (!$data || !preg_match($startDatePattern, $data['startDate'])) {
                    return false;
                }

                if ($data['startDate']) {
                    $data = array_merge($data, ['startDate' => new \DateTime($data['startDate'])]);
                }

                $event->setData($data);
            }
        );
    }

    /**
     * Form default options
     *
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(['data_class' => 'Erp\PaymentBundle\PaySimple\Models\PaySimpleModels\RentPaymentModel']);
    }

    /**
     * Return unique name for this form
     *
     * @return string
     */
    public function getName()
    {
        return 'ps_pay_rent_form';
    }
}
