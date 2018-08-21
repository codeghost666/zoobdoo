<?php
namespace Erp\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Security\Core\Validator\Constraints as SecurityAssert;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Validator\Constraints\Regex;

class DashboardServiceRequestFormType extends AbstractType
{
    /**
     * @var string
     */
    protected $validationGroup = '';

    /**
     * @var string
     */
    protected $translationDomain = 'FOSUserBundle';

    /**
     * @var FormBuilderInterface
     */
    protected $formBuilder;

    /**
     * Construct method
     */
    public function __construct($types = [])
    {
        $this->types = $types;
        $this->validationGroup = 'ServiceRequest';
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array                                        $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->formBuilder = $builder;
        $this
            ->addDate()
            ->addType()
            ->addText()
        ;

        $this->formBuilder->add(
            'save',
            'submit',
            ['label' => 'Submit', 'attr' => ['class' => 'btn red-btn']]
        );
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class'        => 'Erp\UserBundle\Entity\ServiceRequest',
                'validation_groups' => [$this->validationGroup],
            ]
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'erp_users_form_service_request';
    }

    /**
     * Return list service request types
     *
     * @return array
     */
    protected function getTypes()
    {
        return $this->types;
    }

    /**
     * @return $this
     */
    protected function addType()
    {
        $this->formBuilder->add(
            'typeId',
            'choice',
            [
                'label' => ' ',
                'attr' => [
                    'class' => 'form-control select-control',
                ],
                'choices' => $this->getTypes(),
                'required' => true
            ]
        );

        return $this;
    }

    /**
     * @return $this
     */
    protected function addDate()
    {
        $this->formBuilder->add(
            'date',
            'text',
            [
                'label' => ' ',
                'attr' => ['class' => 'form-control date', 'placeholder'=> 'Date'],
                'required' => true,
                'constraints' => [
                    new Regex([
                        'pattern' => '/^[\d]{2}\/[\d]{2}\/[\d]{4}$/',
                        'message' => 'Date isn\'t valid',
                        'groups' => ['ServiceRequest'],
                    ]),
                ]
            ]
        );

        return $this;
    }

    /**
     * @return $this
     */
    protected function addText()
    {
        $this->formBuilder->add(
            'text',
            'textarea',
            [
                'label' => ' ',
                'attr' => [
                    'class' => 'full-width form-control',
                    'placeholder'=> 'Message',
                    'maxlength' => 1000,
                ],
                'required' => true,
            ]
        );

        return $this;
    }
}
