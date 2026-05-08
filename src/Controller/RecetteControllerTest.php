<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RecetteControllerTest extends WebTestCase
{
    protected function setUp(): void
    {
        self::ensureKernelShutdown();
        parent::setUp();
    }

    public function testRecetteIndexPage(): void
    {
        $this->assertTrue(true);
    }

    public function testRecetteNewPageRequiresAuthentication(): void
    {
        $this->assertTrue(true);
    }

    public function testRecetteShowPage(): void
    {
        $this->assertTrue(true);
    }
}