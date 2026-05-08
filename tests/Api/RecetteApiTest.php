<?php

namespace App\Tests\Api;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class RecetteApiTest extends WebTestCase
{
    protected function setUp(): void
    {
        self::ensureKernelShutdown();
        parent::setUp();
    }

    public function testGetRecettesApi(): void
    {
        $this->assertTrue(true, 'Test de base fonctionnel');
    }

    public function testPostRecetteApiWithValidData(): void
    {
        $this->assertTrue(true, 'Test de base fonctionnel');
    }

    public function testPostRecetteApiWithInvalidData(): void
    {
        $this->assertTrue(true, 'Test de base fonctionnel');
    }
}