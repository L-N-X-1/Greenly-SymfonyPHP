<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\AbstractType;
use Karser\Recaptcha3Bundle\Form\Recaptcha3Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Karser\Recaptcha3Bundle\Validator\Constraints\Recaptcha3;


class RegisterUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Le mot de passe et Confirmer le mot de passe doivent correspondre.',
                'options' => ['attr' => ['class' => 'form-control', 'minlength' => '4', 'maxlength' => '25',]],
                'required' => true,
                'first_options'  => ['label' => false, 'error_bubbling' => true],
                'second_options' => ['label' => false],
                'constraints' => [
                    new NotBlank(null, 'Le mot de passe ne peut pas être vide.'),
                    new Length([
                        'min' => 4,
                        'max' => 25,
                        'minMessage' => 'Le mot de passe doit contenir au moins 4 caractères.',
                        'maxMessage' => 'Le mot de passe ne peut pas comporter plus de 25 caractères.',
                    ]),
                ]
            ])
            ->add('fullName',TextType::class,[
                'required' => true,
                'label' => false,
                'attr' => ['name' => 'user[fullName]',
                    'id' => 'user_fullName',
                    'class' => 'form-control',
                ],
                'constraints' => [
                    new NotBlank(null, 'Nom & Prénom ne peut pas être vide.'),
                ],
            ])
            ->add('email',EmailType::class,[
                'required' => true,
                'label' => false,
                'attr' => ['name' => 'user[email]',
                    'id' => 'user_email',
                    'class' => 'form-control',
                ],
                'constraints' => [
                    new NotBlank(null, 'Email ne peut pas être vide.'),
                    new Email(['message' => 'e-mail {{ value }} n’est pas valide.
                                              Une adresse e-mail valide doit ressembler à: exemple@email.com']),
                ],
            ])
            ->add('roles', ChoiceType::class, [
                'label' => 'Roles',
                'choices' => [
                    'Utilisateur' => 'ROLE_USER',
                    'Entreprise' => 'ROLE_ENTREPRISE',
                    'Association' => 'ROLE_ASSOCIATION',
                    'Formateur' => 'ROLE_FORMATEUR',
                    'Donneur' => 'ROLE_DONNEUR',
                ],
                'placeholder' => 'Sélectionnez un rôle',
                'required' => true,
                'multiple' => false, // Only one role allowed
                'expanded' => false, // Dropdown style
                'mapped' => false, 
                'attr' => [
                    'name' => 'user[roles]', // Optional if Symfony automatically assigns names
                    'id' => 'user_roles',
                    'class' => 'form-control',
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Le rôle ne peut pas être vide.']),
                ],
            ])    
            ->add('verifedEntreprise',TextType::class,[
                'required' => false,
                'label' => false,
                'attr' => ['name' => 'user[verifedEntreprise]',
                    'id' => 'user_verifedEntreprise',
                    'class' => 'form-control',
                ],
            ])  
            ->add('verifedAssoc',TextType::class,[
                'required' => false,
                'label' => false,
                'attr' => ['name' => 'user[verifedAssoc]',
                    'id' => 'user_verifedAssoc',
                    'class' => 'form-control',
                ],
            ])   
            ->add('verifedForm',TextType::class,[
                'required' => false,
                'label' => false,
                'attr' => ['name' => 'user[verifedForm]',
                    'id' => 'user_verifedForm',
                    'class' => 'form-control',
                ],
            ]) 
            ->add('verifedDonne',TextType::class,[
                'required' => false,
                'label' => false,
                'attr' => ['name' => 'user[verifedDonne]',
                    'id' => 'user_verifedDonne',
                    'class' => 'form-control',
                ],
            ]) 
            ->add('recaptcha', Recaptcha3Type::class, [
                'constraints' => [
                    new Recaptcha3([
                        'message' => 'There was a problem verifying the reCAPTCHA. Please try again.',
                    ])
                ],
                'attr' => [
                    'data-badge' => 'inline',
                ]
            ])
            ->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
                $form = $event->getForm();
                $data = $event->getData();
                if ($data && $data['roles'] === 'ROLE_ENTREPRISE') {
                    $form->add('verifedEntreprise',TextType::class,[
                        'required' => false,
                        'label' => false,
                        'attr' => ['name' => 'user[verifedEntreprise]',
                            'id' => 'user_verifedEntreprise',
                            'class' => 'form-control',
                        ],
                        'constraints' => [
                            new NotBlank(null, 'Verifed Entreprise cannot be empty.'),
                        ],
                    ]);
                }
                if ($data && $data['roles'] === 'ROLE_ASSOCIATION') {
                    $form->add('verifedAssoc',TextType::class,[
                        'required' => false,
                        'label' => false,
                        'attr' => ['name' => 'user[verifedAssoc]',
                            'id' => 'user_verifedAssoc',
                            'class' => 'form-control',
                        ],
                        'constraints' => [
                            new NotBlank(null, 'Verifed Association cannot be empty.'),
                        ],
                    ]);
                }
                if ($data && $data['roles'] === 'ROLE_FORMATEUR') {
                    $form->add('verifedForm',TextType::class,[
                        'required' => false,
                        'label' => false,
                        'attr' => ['name' => 'user[verifedForm]',
                            'id' => 'user_verifedForm',
                            'class' => 'form-control',
                        ],
                        'constraints' => [
                            new NotBlank(null, 'Verifed Formateur cannot be empty.'),
                        ],
                    ]);
                }
                if ($data && $data['roles'] === 'ROLE_DONNEUR') {
                    $form->add('verifedDonne',TextType::class,[
                        'required' => false,
                        'label' => false,
                        'attr' => ['name' => 'user[verifedDonne]',
                            'id' => 'user_verifedDonne',
                            'class' => 'form-control',
                        ],
                        'constraints' => [
                            new NotBlank(null, 'Verifed Donneur cannot be empty.'),
                        ],
                    ]);
                }
            })  
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'method' => 'POST',
            'attr' => [
                'id' => 'form_register',
                'enctype' => 'multipart/form-data'
            ],
        ]);
    }
}
