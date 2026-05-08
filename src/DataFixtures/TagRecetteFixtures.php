<?php

namespace App\DataFixtures;

use App\Entity\TagRecette;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class TagRecetteFixtures extends Fixture
{
    public const TAGS = [
        'Végétarien' => '#28a745',
        'Végan' => '#20c997',
        'Sans Gluten' => '#ffc107',
        'Bio' => '#6f42c1',
        'Rapide' => '#17a2b8',
        'Familial' => '#fd7e14',
        'Festif' => '#e83e8c',
        'Économique' => '#6c757d',
    ];

    public function load(ObjectManager $manager): void
    {
        foreach (self::TAGS as $nom => $couleur) {
            $tag = new TagRecette();
            $tag->setNom($nom);
            $tag->setCouleur($couleur);
            $manager->persist($tag);
            $this->addReference('tag_' . $nom, $tag);
        }

        $manager->flush();
    }
}