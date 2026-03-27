<?php

namespace App\Tests\Functional\Front;

use App\Tests\Functional\FunctionalTestCase;

class AboutControllerTest extends FunctionalTestCase
{
    /**
     * Test de l'affichage de la page Qui suis-je ?
     */
    public function testShowAboutPage(): void
    {
        $this->get('/about');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h2', 'Qui suis-je ?');
    }
}
