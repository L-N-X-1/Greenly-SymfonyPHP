<?php

namespace App\Form;

use App\Entity\Formation;
use App\Entity\Module;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class FormationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom_formation', null, ['label' => 'Nom de la formation'])
            ->add('description_formation', null, ['label' => 'Description'])
            ->add('duree_formation', IntegerType::class, [
                'label' => 'Durée de la formation (en heures)',
                'attr' => [
                    'min' => 1,
                    'max' => 1000,
                ],
            ])
            ->add('mode_formation', ChoiceType::class, [
                'label' => 'Mode de formation',
                'choices' => [
                    'Sélectionner une option' => null,
                    'Présentiel' => 'presentiel',
                    'Distanciel' => 'distanciel',
                    'Hybride' => 'hybride',
                ],
                'placeholder' => false, // Affiche bien "Sélectionner une option"
                'required' => true, // Rend le champ obligatoire
            ])
            ->add('datedebut_formation', null, [
                'widget' => 'single_text',
                'label' => 'Date de début',
            ])
            ->add('datefin_formation', null, [
                'widget' => 'single_text',
                'label' => 'Date de fin',
            ])
            ->add('module', EntityType::class, [
                'class' => Module::class,
                'choice_label' => 'nom_module', // Afficher le nom du module
                'placeholder' => 'Sélectionner une option', // Ajout de la valeur par défaut
                'required' => true, // Rend le champ obligatoire
                'label' => 'Module',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Formation::class,
        ]);
    }
}
