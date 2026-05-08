<?php

namespace App\DataFixtures;

use App\Entity\CategorieRecette;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CategorieRecetteFixtures extends Fixture
{
    public const CATEGORIES = [
        'Entrée' => ['icone' => '🥗', 'description' => 'Entrées et apéritifs'],
        'Plat' => ['icone' => '🍝', 'description' => 'Plats principaux'],
        'Dessert' => ['icone' => '🍰', 'description' => 'Desserts sucrés'],
        'Boisson' => ['icone' => '🥤', 'description' => 'Boissons et smoothies'],
        'Snack' => ['icone' => '🍕', 'description' => 'Snacks et en-cas'],
        'Soupe' => ['icone' => '🥣', 'description' => 'Soupes et potages'],
    ];

    public function load(ObjectManager $manager): void
    {
        foreach (self::CATEGORIES as $nom => $data) {
            $categorie = new CategorieRecette();
            $categorie->setNom($nom);
            $categorie->setIcone($data['icone']);
            $categorie->setDescription($data['description']);
            $manager->persist($categorie);
            $this->addReference('categorie_' . $nom, $categorie);
        }

        $manager->flush();
    }
}