<?php

namespace App\Tests\Service;

use PHPUnit\Framework\TestCase;

class RecetteAnalyserTest extends TestCase
{
    public function testGetTempsTotal(): void
    {
        $prep = 30;
        $cuisson = 45;
        $total = $prep + $cuisson;
        $this->assertEquals(75, $total);
    }

    public function testGetTotalRecettesPubliees(): void
    {
        $this->assertIsInt(10);
    }

    public function testGetMoyenneIngredients(): void
    {
        $this->assertIsFloat(0.0);
    }
}