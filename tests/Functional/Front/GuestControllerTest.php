<?php

namespace App\Tests\Functional\Front;

use App\Tests\Functional\FunctionalTestCase;

class GuestControllerTest extends FunctionalTestCase
{
    /**
     * Test de l'affichage de la page Invités
     */
    public function testShowGuestsPage(): void
    {
        $this->get('/guests');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h3', 'Invités');
    }
}
