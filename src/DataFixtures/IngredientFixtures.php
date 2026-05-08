<?php

namespace App\DataFixtures;

use App\Entity\Ingredient;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class IngredientFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        
        $ingredientsList = [
            'farine', 'sucre', 'oeufs', 'beurre', 'lait', 'sel', 'poivre',
            'huile d\'olive', 'oignon', 'ail', 'tomate', 'fromage', 'jambon',
            'pommes de terre', 'carottes', 'poulet', 'boeuf', 'poisson'
        ];
        
        $unites = ['g', 'kg', 'ml', 'cl', 'L', 'c. à soupe', 'c. à café', 'pincée', 'pièce(s)'];
        
        // Pour chaque recette, ajouter 3 à 8 ingrédients
        for ($i = 1; $i <= 20; $i++) {
            try {
                $recette = $this->getReference('recette_' . $i, Recette::class);
                $nbIngredients = $faker->numberBetween(3, 8);
                $usedIngredients = [];
                
                for ($j = 1; $j <= $nbIngredients; $j++) {
                    $ingredient = new Ingredient();
                    $nom = $faker->randomElement($ingredientsList);
                    
                    // Éviter les doublons d'ingrédients dans la même recette
                    while (in_array($nom, $usedIngredients)) {
                        $nom = $faker->randomElement($ingredientsList);
                    }
                    $usedIngredients[] = $nom;
                    
                    $quantite = $faker->numberBetween(1, 500) . ' ' . $faker->randomElement($unites);
                    
                    $ingredient->setNom($nom);
                    $ingredient->setQuantite($quantite);
                    $ingredient->setRecette($recette);
                    $manager->persist($ingredient);
                }
            } catch (\Exception $e) {
                // Ignorer les erreurs de référence
                continue;
            }
        }
        
        $manager->flush();
    }
    
    public function getDependencies(): array
    {
        return [RecetteFixtures::class];
    }
}