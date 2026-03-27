<?php

namespace App\Tests\Functional;

use App\Tests\Functional\FunctionalTestCase;

class HomeControllerTest extends FunctionalTestCase
{
    private function canSeeHomePage(): void
    {
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h2', 'Photographe');
    }

    private function canSeeAdminLinks(): void
    {
        $this->assertSelectorExists('ul.nav .nav-item .nav-link:contains("Admin")', 'Le lien "Admin" devrait être visible pour les utilisateurs administrateurs');
        $this->assertSelectorExists('ul.nav .nav-item .nav-link:contains("Déconnexion")', 'Le lien "Déconnexion" devrait être visible pour les utilisateurs administrateurs');
    }

    private function cannotSeeAdminLinks(): void
    {
        $this->assertSelectorNotExists('ul.nav .nav-item .nav-link:contains("Admin")', 'Le lien "Admin" ne devrait pas être visible pour les utilisateurs non administrateurs');
        $this->assertSelectorNotExists('ul.nav .nav-item .nav-link:contains("Déconnexion")', 'Le lien "Déconnexion" ne devrait pas être visible pour les utilisateurs non administrateurs');
    }

    public function testShowHomeAsDisconnectedUser(): void
    {
        $this->get('/');

        $this->canSeeHomePage();
        $this->cannotSeeAdminLinks();
    }

    public function testShowHomeAsUser(): void
    {
        $this->logInAsUser();
        $this->get('/');

        $this->canSeeHomePage();
        $this->cannotSeeAdminLinks();
    }

    public function testShowHomeAsAdmin(): void
    {
        $this->logInAsAdmin();
        $this->get('/');

        $this->canSeeHomePage();
        $this->canSeeAdminLinks();
    }

    public function testShowHomeAsSuperAdmin(): void
    {
        $this->logInAsSuperAdmin();
        $this->get('/');

        $this->canSeeHomePage();
        $this->canSeeAdminLinks();
    }
}
