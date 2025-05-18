<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Karser\Recaptcha3Bundle\Form\Recaptcha3Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Karser\Recaptcha3Bundle\Validator\Constraints\Recaptcha3;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email',EmailType::class,[
                'required' => true,
                'label' => 'Email',
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
            ->add('fullName',TextType::class,[
                'label' => 'Name',
                'required' => true,
                'attr' => ['name' => 'user[fullName]',
                    'id' => 'user_fullName',
                    'class' => 'form-control',
                ],
                'constraints' => [
                    new NotBlank(null, 'Nom & Prénom ne peut pas être vide.'),
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
            ->add('imgFile', FileType::class, [
                'label' => 'Image',
                'required' => false,
                'data_class' => null,
                'attr' => ['name' => 'user[imgFile]',
                    'id' => 'user_imgFile',
                    'class' => 'form-control',
                    'accept' => '.jpeg,.jpg,.png',
                ],
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Password and Confirm Password must match.',
                'options' => ['attr' => ['class' => 'form-control', 'minlength' => '4', 'maxlength' => '25',]],
                'required' => true,
                'first_options'  => ['label' => false, 'error_bubbling' => true],
                'second_options' => ['label' => false],
                'constraints' => [
                    new NotBlank(null, 'The password cannot be empty.'),
                    new Length([
                        'min' => 4,
                        'max' => 25,
                        'minMessage' => 'The password must contain at least 4 characters.',
                        'maxMessage' => 'The password cannot be more than 25 characters long.',
                    ]),
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'attr' => [
                'id' => 'form_user',
                'enctype' => 'multipart/form-data'
            ],
        ]);
    }
}
