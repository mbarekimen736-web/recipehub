<?php

namespace App\DataFixtures;

use App\Entity\Recette;
use App\Entity\Ingredient;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use App\Entity\CategorieRecette;
use App\Entity\TagRecette;
use App\Entity\User;

class RecetteFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        
        $categories = [
            $this->getReference('categorie_Entrée', CategorieRecette::class),
            $this->getReference('categorie_Plat', CategorieRecette::class),
            $this->getReference('categorie_Dessert', CategorieRecette::class),
            $this->getReference('categorie_Boisson', CategorieRecette::class),
            $this->getReference('categorie_Snack', CategorieRecette::class),
            $this->getReference('categorie_Soupe', CategorieRecette::class),
        ];
        
        $tags = [
            $this->getReference('tag_Végétarien', TagRecette::class),
            $this->getReference('tag_Végan', TagRecette::class),
            $this->getReference('tag_Sans Gluten', TagRecette::class),
            $this->getReference('tag_Bio', TagRecette::class),
            $this->getReference('tag_Rapide', TagRecette::class),
            $this->getReference('tag_Familial', TagRecette::class),
            $this->getReference('tag_Festif', TagRecette::class),
            $this->getReference('tag_Économique', TagRecette::class),
        ];
        
        $auteurs = [
            $this->getReference('user_admin', User::class),
            $this->getReference('user_cuisinier', User::class),
            $this->getReference('user_1', User::class),
            $this->getReference('user_2', User::class),
            $this->getReference('user_3', User::class),
            $this->getReference('user_4', User::class),
            $this->getReference('user_5', User::class),
        ];
        
        $difficultes = ['facile', 'moyen', 'difficile'];
        
        // Créer 20 recettes
        for ($i = 1; $i <= 20; $i++) {
            $recette = new Recette();
            $recette->setTitre($faker->sentence(3));
            $recette->setDescription($faker->paragraph(2));
            $recette->setInstructions($faker->paragraphs(3, true));
            $recette->setTempsPreparation($faker->numberBetween(5, 60));
            $recette->setTempsCuisson($faker->optional(0.7, null)->numberBetween(10, 120));
            $recette->setDifficulte($faker->randomElement($difficultes));
            $recette->setNbPersonnes($faker->numberBetween(1, 12));
            $recette->setPubliee($faker->boolean(80));
            $recette->setDateCreation($faker->dateTimeBetween('-6 months', 'now'));
            
            // Associer une catégorie
            $recette->setCategorie($faker->randomElement($categories));
            
            // Associer un auteur
            $recette->setAuteur($faker->randomElement($auteurs));
            
            // Associer 1 à 4 tags aléatoires
            $randomTags = $faker->randomElements($tags, $faker->numberBetween(1, 4));
            foreach ($randomTags as $tag) {
                $recette->addTag($tag);
            }
            
            $manager->persist($recette);
            $this->addReference('recette_' . $i, $recette);
        }
        
        $manager->flush();
    }
    
    public function getDependencies(): array
    {
        return [
            CategorieRecetteFixtures::class,
            TagRecetteFixtures::class,
            UserFixtures::class,
        ];
    }
}