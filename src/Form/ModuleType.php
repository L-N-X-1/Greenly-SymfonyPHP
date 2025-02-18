<?php

namespace App\Form;

use App\Entity\Module;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ModuleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
    ->add('nom_module')
    ->add('description_module')
    ->add('nb_heures')
    ->add('niveau', ChoiceType::class, [
        'choices' => [
            'Débutant' => 'Débutant',
            'Intermédiaire' => 'Intermédiaire',
            'Avancé' => 'Avancé',
            'Expert' => 'Expert',
        ],
        'placeholder' => 'Sélectionner une option',  // Correct placement of the placeholder
        'expanded' => false, // false means a dropdown, true means radio buttons
        'multiple' => false, // false means single choice
    ])
    
    ->add('categorie', ChoiceType::class, [
        'choices' => [
            'Verre' => 'Verre',
            'Plastique' => 'Plastique',
            'Électronique' => 'Électronique',
            'Papier' => 'Papier',
            'Métaux' => 'Métaux',
        ],
        'placeholder' => 'Sélectionner une option',  // Correct placement of the placeholder
        'expanded' => false, // false means a dropdown, true means radio buttons
        'multiple' => false, // false means single choice
    ])
    
    ->add('statut', CheckboxType::class, [
        'required' => false, // Permet de ne pas obliger à cocher la case
        'label' => 'Statut',
    ])
    
    ->add('datecreation_module', DateType::class, [
        'label' => 'Date de Création',
        'widget' => 'single_text',
        'attr' => ['class' => 'form-control'],
    ]);
}

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Module::class,
        ]);
    }
}

