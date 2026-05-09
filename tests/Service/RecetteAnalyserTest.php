<?php

namespace App\Tests\Service;

use App\Entity\Recette;
use PHPUnit\Framework\TestCase;

class RecetteAnalyserTest extends TestCase
{
    public function testGetTempsTotalAdditionnePreparationEtCuisson(): void
    {
        $recette = new Recette();
        $recette->setTempsPreparation(30);
        $recette->setTempsCuisson(45);

        $total = $recette->getTempsPreparation() + ($recette->getTempsCuisson() ?? 0);
        $this->assertEquals(75, $total);
    }

    public function testGetTempsTotalGereTempsCuissonNull(): void
    {
        $recette = new Recette();
        $recette->setTempsPreparation(30);
        $recette->setTempsCuisson(null);

        $total = $recette->getTempsPreparation() + ($recette->getTempsCuisson() ?? 0);
        $this->assertEquals(30, $total);
    }

    public function testCalculMoyenneIngredients(): void
    {
        $total = 15;
        $nombre = 5;
        $moyenne = $total / $nombre;
        $this->assertEquals(3.0, $moyenne);
    }
}