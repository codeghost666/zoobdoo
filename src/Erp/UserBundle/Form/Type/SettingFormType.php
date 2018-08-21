<?php
namespace Erp\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Security\Core\Validator\Constraints as SecurityAssert;

class SettingFormType extends AbstractType
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
     * SettingFormType constructor.
     *
     * @param array $settings
     */
    public function __construct($settings)
    {
        $this->settings = $settings;
        $this->validationGroup = 'Settings';
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array                                        $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->formBuilder = $builder;

        $this->addSettings()->addSecondEmail();

        $this->formBuilder->add(
            'save',
            'submit',
            ['label' => 'Save all changes', 'attr' => ['class' => 'btn edit-btn']]
        );
    }

    /**
     * @return $this
     */
    public function addSettings()
    {
        $this->formBuilder->add(
            'settings',
            'choice',
            [
                'label' => ' ',
                'choices' => $this->settings,
                'multiple' => true,
                'expanded' => true,
                'attr' => []
            ]
        );

        return $this;
    }

    /**
     * @return $this
     */
    public function addSecondEmail()
    {
        $this->formBuilder->add(
            'secondEmail',
            'email',
            [
                'label' => 'Send Email Notifications to:',
                'attr' => ['class' => 'form-control settings-email']
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
                'data_class'        => 'Erp\UserBundle\Entity\User',
                'validation_groups' => [$this->validationGroup]
            ]
        );
    }

    public function getName()
    {
        return 'erp_users_form_settings';
    }
}
