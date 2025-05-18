<?php

namespace App\Form;

use App\Entity\Event;
use App\Entity\Sponsor;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;

class EventType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('event_name', null, [
                'label' => 'Nom de l\'événement'
            ])
            ->add('event_date', DateTimeType::class, [
                'label' => 'Date et heure',
                'widget' => 'single_text',
                'html5' => true,
                'input' => 'datetime',
                'required' => true,
                'attr' => [
                    'class' => 'form-control',
                    'type' => 'datetime-local'
                ]
            ])
            ->add('event_description', null, [
                'label' => 'Description'
            ])
            ->add('event_location', ChoiceType::class, [
                'choices' => [
                    'Tunis' => 'Tunis',
                    'Ariana' => 'Ariana',
                    'Beja' => 'Beja',
                    'Ben Arous' => 'Ben Arous',
                    'Bizerte' => 'Bizerte',
                    'Gabes' => 'Gabes',
                    'Gafsa' => 'Gafsa',
                    'Jendouba' => 'Jendouba',
                    'Kairouan' => 'Kairouan',
                    'Kasserine' => 'Kasserine',
                    'Kebili' => 'Kebili',
                    'Kef' => 'Kef',
                    'Mahdia' => 'Mahdia',
                    'Manouba' => 'Manouba',
                    'Medenine' => 'Medenine',
                    'Monastir' => 'Monastir',
                    'Nabeul' => 'Nabeul',
                    'Sfax' => 'Sfax',
                    'Sidi Bouzid' => 'Sidi Bouzid',
                    'Siliana' => 'Siliana',
                    'Sousa' => 'Sousa',
                    'Tataouine' => 'Tataouine',
                    'Tozeur' => 'Tozeur',
                    'Zaghouan' => 'Zaghouan',
                ],
                
            ])
            ->add('event_status', ChoiceType::class, [
                'choices' => [
                    'En cours' => Event::STATUS_EN_COURS,
                    'Planifié' => Event::STATUS_PLANIFIE,
                    'Achevée' => Event::STATUS_ACHEVE,
                ]
            ])
            
            ->add('user', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'fullName', // Ensure `fullName` exists in User entity
                'label' => 'Organisateur',
                'placeholder' => 'Sélectionner un organisateur',
                'attr' => ['class' => 'form-control'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Event::class,
        ]);
    }
}
