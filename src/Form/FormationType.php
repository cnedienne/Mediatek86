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
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre de la formation'
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description de la formation',
                'required' => false
            ])
            ->add('playlist', EntityType::class, [
                'class' => Playlist::class,
                'choice_label' => 'name',
                'label' => 'Playlist associée'
            ])
            ->add('categories', EntityType::class, [
                'class' => Categorie::class,
                'choice_label' => 'name',
                'multiple' => true,
                'label' => 'Catégories',
                'required' => false
            ])
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

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Formation::class,
        ]);
    }
}
