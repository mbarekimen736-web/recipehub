<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RecetteControllerTest extends WebTestCase
{
    public function testRecetteIndexPage(): void
    {
        $client = static::createClient();
        $client->request('GET', '/recette/');
        
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
    
    public function testRecetteShowPage(): void
    {
        $client = static::createClient();
        $client->request('GET', '/recette/1');
        
        // 200 si la recette existe, 404 sinon
        $statusCode = $client->getResponse()->getStatusCode();
        $this->assertContains($statusCode, [200, 404]);
    }
    
    public function testRecetteNewPageRequiresAuthentication(): void
    {
        $client = static::createClient();
        $client->request('GET', '/recette/new');
        
        // Devrait rediriger vers login (302)
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
    }
}
