<?php

namespace App\Form;

use App\Entity\Recette;
use App\Entity\CategorieRecette;
use App\Entity\TagRecette;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class RecetteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre', TextType::class)
            ->add('description', TextareaType::class)
            ->add('instructions', TextareaType::class)
            ->add('tempsPreparation', IntegerType::class)
            ->add('tempsCuisson', IntegerType::class, ['required' => false])
            ->add('difficulte', ChoiceType::class, [
                'choices' => ['facile' => 'facile', 'moyen' => 'moyen', 'difficile' => 'difficile'],
            ])
            ->add('nbPersonnes', IntegerType::class)
            ->add('categorie', EntityType::class, [
                'class' => CategorieRecette::class,
                'choice_label' => 'nom',
            ])
            ->add('tags', EntityType::class, [
                'class' => TagRecette::class,
                'choice_label' => 'nom',
                'multiple' => true,
                'expanded' => true,
                'by_reference' => false,
                'required' => false,
            ])
            ->add('publiee', CheckboxType::class, ['required' => false])
            ->add('image', FileType::class, ['mapped' => false, 'required' => false, 'constraints' => [new Assert\Image(['maxSize' => '2M', 'mimeTypes' => ['image/jpeg','image/png','image/webp']])]])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Recette::class,
        ]);
    }
}
