<?php

namespace Erp\PaymentBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class PaySimpleHistoryExportFormType
 *
 * @package Erp\PaymentBundle\Form\Type
 */
class PaySimpleHistoryExportFormType extends AbstractType
{
    /**
     * Build form function
     *
     * @param FormBuilderInterface $builder the formBuilder
     * @param array                $options the options for this form
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'startDate',
            'text',
            [
                'label'       => 'Start Date:',
                'label_attr'  => ['class' => 'control-label'],
                'attr'        => ['class' => 'form-control col-xs-4 date'],
                'required'    => true,
                'constraints' => [
                    new Assert\Date(['message' => 'Is not a valid date format'])
                ],
                'required'    => false,
            ]
        )
        ->add(
            'endDate',
            'text',
            [
                'label'       => 'End Date:',
                'label_attr'  => ['class' => 'control-label'],
                'attr'        => ['class' => 'form-control col-xs-4 date'],
                'required'    => true,
                'constraints' => [
                    new Assert\Date(['message' => 'Is not a valid date format'])
                ],
                'required'    => false,
            ]
        )
        ->add('pdf_submit', 'submit', ['label' => 'pdf', 'attr' => ['class' => 'btn edit-btn']])
        ->add('csv_submit', 'submit', ['label' => 'csv', 'attr' => ['class' => 'btn edit-btn']]);

        $builder->addEventListener(
            FormEvents::PRE_SUBMIT,
            function (FormEvent $event) {
                $data = $event->getData();
                if (!$data) {
                    return false;
                }

                if ($data['startDate']) {
                    $data = array_merge($data, ['startDate' => new \DateTime($data['startDate'])]);
                }

                if ($data['endDate']) {
                    $data = array_merge($data, ['endDate' => new \DateTime($data['endDate'])]);
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
        $resolver->setDefaults(['data_class' => 'Erp\PaymentBundle\Form\Models\PaymentHistoryExportModel']);
    }

    /**
     * Return unique name for this form
     *
     * @return string
     */
    public function getName()
    {
        return 'ps_history_export_form';
    }
}
