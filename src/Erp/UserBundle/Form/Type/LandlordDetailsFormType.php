<?php
namespace Erp\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Security\Core\Validator\Constraints as SecurityAssert;

class LandlordDetailsFormType extends TenantDetailsFormType
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
        $this->validationGroup = 'LandlordDetails';
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array                                        $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $this->formBuilder = $builder;
        $this->addCompanyName();
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class'        => 'Erp\UserBundle\Entity\User',
                'validation_groups' => [$this->validationGroup]
            ]
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'erp_users_landlord_form_details';
    }

    /**
     * @return $this
     */
    private function addCompanyName()
    {
        $this->formBuilder->add(
            'companyName',
            'text',
            [
                'label'              => 'Company Name',
                'label_attr'         => ['class' => 'control-label'],
                'attr'               => ['class' => 'form-control contact-email full-width'],
                'translation_domain' => $this->translationDomain,
                'required'           => false
            ]
        );

        return $this;
    }
}
