<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Karser\Recaptcha3Bundle\Form\Recaptcha3Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Karser\Recaptcha3Bundle\Validator\Constraints\Recaptcha3;

class UserForgotPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email',EmailType::class,[
                'required' => true,
                'label' => false,
                'attr' => ['name' => 'forgotPassword[email]',
                    'id' => 'forgotPassword_email',
                    'class' => 'form-control',
                    'placeholder' => 'Entrez votre adresse e-mail de connexion',
                ],
                'constraints' => [
                    new NotBlank(null, 'Email ne peut pas être vide.'),
                    new Email(['message' => 'e-mail {{ value }} n’est pas valide.
                                              Une adresse e-mail valide doit ressembler à: exemple@email.com'])
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
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'attr' => ['id' => 'forgotPassword-form'],
            'method' => 'POST'
        ]);
    }
}
