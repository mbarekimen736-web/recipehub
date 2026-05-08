<?php

namespace App\Form;

use App\Entity\Recette;
use App\Entity\CategorieRecette;
use App\Entity\TagRecette;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Range;

class RecetteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre', TextType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'Le titre est obligatoire']),
                    new Length(['min' => 5, 'minMessage' => 'Le titre doit faire au moins 5 caractères'])
                ]
            ])
            ->add('description', TextareaType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'La description est obligatoire']),
                    new Length(['min' => 30, 'minMessage' => 'La description doit faire au moins 30 caractères'])
                ]
            ])
            ->add('instructions', TextareaType::class, [
                'constraints' => [new NotBlank(['message' => 'Les instructions sont obligatoires'])]
            ])
            ->add('tempsPreparation', IntegerType::class, [
                'constraints' => [new Range(['min' => 1, 'minMessage' => 'Le temps doit être au moins 1 minute'])]
            ])
            ->add('tempsCuisson', IntegerType::class, [
                'required' => false
            ])
            ->add('difficulte', ChoiceType::class, [
                'choices' => [
                    'Facile' => 'facile',
                    'Moyen' => 'moyen',
                    'Difficile' => 'difficile'
                ]
            ])
            ->add('nbPersonnes', IntegerType::class, [
                'constraints' => [new Range(['min' => 1, 'max' => 50])]
            ])
            ->add('publiee', null, [
                'required' => false
            ])
            ->add('categorie', EntityType::class, [
                'class' => CategorieRecette::class,
                'choice_label' => 'nom',
                'placeholder' => 'Choisir une catégorie'
            ])
            ->add('tags', EntityType::class, [
                'class' => TagRecette::class,
                'choice_label' => 'nom',
                'multiple' => true,
                'expanded' => true,
                'required' => false,
                'by_reference' => false
            ])
            ->add('imageFile', FileType::class, [
                'label' => 'Image de la recette (JPEG, PNG, WebP)',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '2M',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                            'image/webp'
                        ],
                        'mimeTypesMessage' => 'Veuillez uploader une image au format JPEG, PNG ou WebP',
                        'maxSizeMessage' => 'L\'image ne doit pas dépasser 2 Mo'
                    ])
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Recette::class,
            'csrf_protection' => true
        ]);
    }
}