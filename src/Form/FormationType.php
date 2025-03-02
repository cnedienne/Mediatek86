<?php
// src/Form/FormationType.php

namespace App\Form;

use App\Entity\Formation;
use App\Entity\Playlist;
use App\Entity\Categorie;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Validator\Constraints\LessThanOrEqual;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FormationType extends AbstractType
{
    // Méthode pour construire le formulaire
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // Ajout d'un champ texte pour le titre de la formation
            ->add('title', TextType::class, [
                'label' => 'Titre de la formation'
            ])
            // Ajout d'un champ textarea pour la description de la formation
            ->add('description', TextareaType::class, [
                'label' => 'Description de la formation',
                'required' => false
            ])
            // Ajout d'un champ EntityType pour sélectionner une playlist associée
            ->add('playlist', EntityType::class, [
                'class' => Playlist::class,
                'choice_label' => 'name',
                'label' => 'Playlist associée'
            ])
            // Ajout d'un champ EntityType pour sélectionner plusieurs catégories
            ->add('categories', EntityType::class, [
                'class' => Categorie::class,
                'choice_label' => 'name',
                'multiple' => true,
                'label' => 'Catégories',
                'required' => false
            ])
            // Ajout d'un champ DateType pour la date de publication
            ->add('publishedAt', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Date de publication',
                'constraints' => [
                    new LessThanOrEqual([
                        'value' => 'today',
                        'message' => 'La date de publication ne peut pas être dans le futur.',
                    ]), 
                ],
            ]);
    }

    // Méthode pour configurer les options du formulaire
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Formation::class,
        ]);
    }
}
