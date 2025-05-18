<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Karser\Recaptcha3Bundle\Form\Recaptcha3Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Karser\Recaptcha3Bundle\Validator\Constraints\Recaptcha3;

class ResetUserPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Password fields must match.',
                'options' => ['attr' => ['class' => 'form-control']],
                'required' => true,
                'first_options'  => ['label' => false, 'error_bubbling' => true],
                'second_options' => ['label' => false],
                'constraints' => [
                    new NotBlank(null, 'Password cannot be empty.'),
                ]
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
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'attr' => ['id' => 'reset-user-password-form',
                'enctype' => 'multipart/form-data'],
        ]);
    }
}
