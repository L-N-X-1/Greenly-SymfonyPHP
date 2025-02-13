<?php

namespace App\Form;

use App\Entity\Event;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EventType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('event_name', TextType::class, [
                'label' => 'Event Name'
            ])
            ->add('event_description', TextareaType::class, [
                'label' => 'Description'
            ])
            ->add('event_date', DateTimeType::class, [
                'label' => 'Event Date'
            ])
            ->add('event_location', TextType::class, [
                'label' => 'Location'
            ])
            ->add('event_statut', TextType::class, [
                'label' => 'Status'
            ])
            ->add('organizer_id', TextType::class, [
                'label' => 'Organizer ID'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Event::class,
        ]);
    }
}
