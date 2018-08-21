<?php

namespace Erp\UserBundle\Form\Type;

use Symfony\Component\Validator\Constraints as Assert;
use Erp\UserBundle\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityRepository;
use Erp\CoreBundle\Form\ImageType;

class ManagerRegistrationFormType extends AbstractType {

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
     * @var array
     */
    protected $states;

    /**
     * @var bool
     */
    protected $invitedUser;

    /**
     * Construct method
     */
    public function __construct(Request $request, $states = null, $invitedUser = null) {
        $this->request = $request;
        $this->states = $states;
        $this->invitedUser = $invitedUser;
        $this->validationGroup = 'ManagerRegister';
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array                                        $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $this->formBuilder = $builder;
        $this->addCompanyName()
                ->addRole()
                ->addFirstName()
                ->addLastName()
                ->addPhone()
                ->addWebsiteUrl()
                ->addAddressOne()
                ->addAddressTwo()
                ->addCity()
                ->addState()
                ->addPostalCode()
                ->addEmail()
                ->addPlainPassword()
                ->addIsTermOfUse()
                ->addLogo()
        ;

        $this->formBuilder->add(
                'stripeAccount', 'erp_stripe_bank_account_verification', [
            'constraints' => new Assert\Valid()
                ]
        );

        $this->formBuilder->add(
                'save', 'submit', ['label' => 'Submit', 'attr' => ['class' => 'btn submit-popup-btn', 'disabled' => 'disabled']]
        );
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(array(
            'data_class' => User::class,
            'validation_groups' => ['UserTermOfUse', 'ManagerRegister']
        ));
    }

    public function getName() {
        return 'erp_users_manager_form_registration';
    }

    /**
     * Return USA states
     *
     * @return array
     */
    protected function getStates() {
        return $this->states;
    }

    /**
     * @return $this
     */
    private function addCompanyName() {
        $this->formBuilder->add(
                'companyName', 'text', [
            'label' => 'Company Name',
            'label_attr' => ['class' => 'control-label'],
            'attr' => ['class' => 'form-control contact-email full-width'],
            'translation_domain' => $this->translationDomain,
            'required' => false
                ]
        );

        return $this;
    }

    /**
     * @return $this
     */
    private function addFirstName() {
        $this->formBuilder->add(
                'firstName', 'text', [
            'label' => 'First Name',
            'label_attr' => ['class' => 'control-label required-label'],
            'attr' => ['class' => 'form-control'],
            'translation_domain' => $this->translationDomain,
            'required' => true,
                ]
        );

        return $this;
    }

    /**
     * @return $this
     */
    private function addLastName() {
        $this->formBuilder->add(
                'lastName', 'text', [
            'label' => 'Last Name',
            'label_attr' => ['class' => 'control-label required-label'],
            'attr' => ['class' => 'form-control'],
            'translation_domain' => $this->translationDomain,
            'required' => true,
                ]
        );

        return $this;
    }

    /**
     * @return $this
     */
    private function addPhone() {
        $this->formBuilder->add(
                'phone', null, [
            'label' => 'Phone Number',
            'label_attr' => ['class' => 'control-label required-label'],
            'attr' => ['class' => 'form-control', 'placeholder' => '555-555-5555'],
            'translation_domain' => $this->translationDomain,
            'required' => true
                ]
        );

        return $this;
    }

    /**
     * @return $this
     */
    private function addWebsiteUrl() {
        $this->formBuilder->add(
                'websiteUrl', 'url', [
            'label' => 'Website',
            'label_attr' => ['class' => 'control-label'],
            'attr' => ['class' => 'form-control contact-email full-width'],
            'translation_domain' => $this->translationDomain,
            'required' => false
                ]
        );

        return $this;
    }

    /**
     * @return $this
     */
    private function addAddressOne() {
        $this->formBuilder->add(
                'addressOne', 'textarea', [
            'label' => 'Address',
            'label_attr' => ['class' => 'control-label required-label'],
            'attr' => ['class' => 'form-control'],
            'translation_domain' => $this->translationDomain,
            'required' => true
                ]
        );

        return $this;
    }

    /**
     * @return $this
     */
    private function addAddressTwo() {
        $this->formBuilder->add(
                'addressTwo', 'textarea', [
            'label' => 'Address 2',
            'label_attr' => ['class' => 'control-label'],
            'attr' => ['class' => 'form-control'],
            'translation_domain' => $this->translationDomain,
            'required' => false
                ]
        );

        return $this;
    }

    /**
     * @return $this
     */
    private function addCity() {
        $formData = $this->request->request->get($this->getName());

        $currentState = $this->formBuilder->getData()->getState();
        $selectedState = $formData['state'];
        $selectedState = ($selectedState) ? $selectedState : $currentState
        ;

        $this->formBuilder->add(
                'city', 'entity', [
            'label' => 'City',
            'class' => 'Erp\CoreBundle\Entity\City',
            'label_attr' => ['class' => 'control-label required-label'],
            'attr' => [
                'class' => 'form-control select-control',
                'aria-labelledby' => "dLabel",
                'data-class' => 'cities'
            ],
            'query_builder' => function (EntityRepository $er) use ($currentState, $selectedState) {
                $state = ($currentState !== $selectedState) ? $selectedState : $currentState
                ;

                return $er->getCitiesByStateCodeQb($state);
            },
            'translation_domain' => $this->translationDomain,
            'required' => true
                ]
        );

        return $this;
    }

    /**
     * @return $this
     */
    private function addState() {
        $this->formBuilder->add(
                'state', 'choice', [
            'label' => 'State',
            'label_attr' => ['class' => 'control-label required-label'],
            'attr' => [
                'class' => 'form-control select-control',
                'aria-labelledby' => "dLabel",
                'data-class' => 'states'
            ],
            'choices' => $this->getStates(),
            'multiple' => false,
            'required' => false,
            'translation_domain' => $this->translationDomain,
                ]
        );

        return $this;
    }

    /**
     * @return $this
     */
    private function addPostalCode() {
        $this->formBuilder->add(
                'postalCode', 'text', [
            'label' => 'ZIP',
            'label_attr' => ['class' => 'control-label required-label'],
            'attr' => ['class' => 'form-control'],
            'translation_domain' => $this->translationDomain,
            'required' => true
                ]
        );

        return $this;
    }

    /**
     * @return $this
     */
    private function addEmail() {
        if ($this->invitedUser) {
            $disabled = true;
        } else {
            $disabled = false;
        }

        $this->formBuilder->add(
                'email', 'email', [
            'label' => 'Email',
            'label_attr' => ['class' => 'control-label required-label'],
            'attr' => ['class' => 'form-control'],
            'translation_domain' => $this->translationDomain,
            'invalid_message' => 'Email is invalid',
            'required' => true,
            'disabled' => $disabled,
            'constraints' => [
                new Assert\NotBlank(
                        [
                    'message' => 'Please enter your Email',
                    'groups' => [$this->validationGroup],
                        ]
                ),
                new Assert\Length(
                        [
                    'min' => 6,
                    'max' => 255,
                    'minMessage' => 'Email should have minimum 6 characters and maximum 255 characters',
                    'maxMessage' => 'Email should have minimum 6 characters and maximum 255 characters',
                    'groups' => [$this->validationGroup],
                        ]
                ),
                new Assert\Email(
                        [
                    'message' => 'This value is not a valid Email address.
                                          Use following formats: example@address.com',
                    'groups' => [$this->validationGroup],
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
    private function addIsTermOfUse() {
        $this->formBuilder->add(
                'isTermOfUse', 'checkbox', [
            'required' => true
                ]
        );

        return $this;
    }

    /**
     * @return $this
     */
    private function addPlainPassword() {
        $this->formBuilder->add(
                'plainPassword', 'repeated', [
            'type' => 'password',
            'options' => ['translation_domain' => $this->translationDomain],
            'first_options' => [
                'label' => 'Password',
                'label_attr' => ['class' => 'control-label required-label'],
                'attr' => ['class' => 'form-control']
            ],
            'second_options' => [
                'label' => 'Repeat Password',
                'label_attr' => ['class' => 'control-label required-label'],
                'attr' => ['class' => 'form-control']
            ],
            'invalid_message' => 'Password mismatch',
            'trim' => false,
            'required' => true,
            'constraints' => [
                new Assert\NotBlank(
                        [
                    'message' => 'Please enter your password',
                    'groups' => [$this->validationGroup],
                        ]
                ),
                new Assert\Regex(
                        [
                    'pattern' => '/^(?=.{5,255})(?=.*[a-zA-Z])(?=.*\d)(?=.*[\W])(?!.*\s).*$/',
                    'message' => 'The password must contain letters, numbers and
                                special characters (example: &#@%!$) and must not have spaces',
                    'groups' => [$this->validationGroup],
                        ]
                )
            ],
                ]
        );

        return $this;
    }

    /**
     * @return $this
     */
    private function addLogo() {
        $this->formBuilder->add(
                'logo', new ImageType(), [
            'required' => false,
            'label' => 'Company logo'
                ]
        );
    }

    private function addRole() {
        $this->formBuilder->add(
                'role', 'choice', [
            'label' => 'Account type',
            'label_attr' => ['class' => 'control-label required-label'],
            'attr' => [
                'class' => 'form-control select-control'
            ],
            'choices' => [
                User::ROLE_MANAGER => 'Manager',
                User::ROLE_LANDLORD => 'Landlord'
            ],
            'multiple' => false,
            'required' => true,
            'translation_domain' => $this->translationDomain,
                ]
        );

        return $this;
    }

}
