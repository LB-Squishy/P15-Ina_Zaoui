<?php

namespace App\Tests\Functional;

use App\Tests\Functional\FunctionalTestCase;

class HomepageTest extends FunctionalTestCase
{
    public function testShowHomepage(): void
    {
        $this->get('/');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h2', 'Photographe');
    }
}
