<?php

namespace App\Form;

use App\Entity\Produit;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProduitType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom')
            ->add('prix', NumberType::class, [
                'scale' => 2, // Permettre les décimales
                'html5' => true,
                'attr' => ['step' => '100'],
            ])
            ->add('description')
            ->add('categorie', ChoiceType::class, [
                'choices' => [
                    '♻️ Plastique' => 'plastique',
                    '👕 Vêtement' => 'vetement',
                    '🪑 Meuble' => 'meuble',
                ],
                'placeholder' => 'Choisir une catégorie',
                'attr' => ['class' => 'form-select'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Produit::class,
        ]);
    }
}