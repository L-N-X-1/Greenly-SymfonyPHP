<?php

namespace App\Form;

use App\Entity\Sponsor;
use App\Entity\Event;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SponsorType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('sponsor_name', TextType::class, [
                'label' => 'Sponsor Name'
            ])
            ->add('event', EntityType::class, [
                'class' => Event::class,
                'choice_label' => 'event_name',
                'label' => 'Event'
            ])
            ->add('product_id', NumberType::class, [
                'label' => 'Product ID'
            ])
            ->add('montant', NumberType::class, [
                'label' => 'Amount'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sponsor::class,
        ]);
    }
}
