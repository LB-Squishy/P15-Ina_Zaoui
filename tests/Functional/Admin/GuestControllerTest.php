<?php

namespace App\Tests\Functional\Admin;

use App\Repository\GuestRepository;
use App\Tests\Functional\FunctionalTestCase;

class GuestControllerTest extends FunctionalTestCase
{
    /////////////////////////////////////////////////////////////////////-----TEST D'AFFICHAGE DE LA PAGE DE GESTION DES INVITÉS-----////////////////////////////////////////////////////////////////////

    /**
     * Test de l'affichage de la page /admin/guest pour un super admin
     */
    public function testShowAdminGuestPageForSuperAdmin(): void
    {
        $user = $this->loginAsSuperAdmin();
        $crawler = $this->get('/admin/guest');
        $this->assertResponseIsSuccessful();
    }

    /**
     * Test de refus d'accès à la page /admin/guest pour un admin
     */
    public function testIfAccessDeniedForAdmin(): void
    {
        $this->loginAsAdmin();
        $this->get('/admin/guest');
        $this->assertResponseStatusCodeSame(403);
    }

    /**
     * Test de refus d'accès à la page /admin/guest pour un utilisateur désactivé
     */
    public function testIfAccessDeniedForUser(): void
    {
        $this->loginAsUser();
        $this->get('/admin/guest');
        $this->assertResponseStatusCodeSame(403);
    }
}
