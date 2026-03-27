<?php

namespace App\Tests\Functional;

use App\Tests\Functional\FunctionalTestCase;

class HomeControllerTest extends FunctionalTestCase
{
    /**
     * Test de l'affichage de la page d'accueil
     * - en tant qu'utilisateur non connecté
     * - et de l'abscence des liens "Admin" et "Déconnexion"
     */
    public function testShowHomeAsDisconnectedUser(): void
    {
        $this->get('/');

        $this->canSeeHomePage();
        $this->cannotSeeAdminLinks();
    }

    /**
     * Test de l'affichage de la page d'accueil
     * - en tant que simple utilisateur connecté
     * - et de l'absence des liens "Admin" et "Déconnexion"
     */
    public function testShowHomeAsUser(): void
    {
        $this->logInAsUser();
        $this->get('/');

        $this->canSeeHomePage();
        $this->cannotSeeAdminLinks();
    }

    /**
     * Test de l'affichage de la page d'accueil
     * - en tant qu'administrateur connecté
     * - et de la présence des liens "Admin" et "Déconnexion"
     */
    public function testShowHomeAsAdmin(): void
    {
        $this->logInAsAdmin();
        $this->get('/');

        $this->canSeeHomePage();
        $this->canSeeAdminLinks();
    }

    /**
     * Test de l'affichage de la page d'accueil
     * - en tant que super administrateur connecté
     * - et de la présence des liens "Admin" et "Déconnexion"
     */
    public function testShowHomeAsSuperAdmin(): void
    {
        $this->logInAsSuperAdmin();
        $this->get('/');

        $this->canSeeHomePage();
        $this->canSeeAdminLinks();
    }

    /**
     * Fonction pour vérifier l'affichage de la page d'accueil
     */
    private function canSeeHomePage(): void
    {
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h2', 'Photographe');
    }

    /**
     * Fonction pour vérifier la présence des liens "Admin" et "Déconnexion"
     */
    private function canSeeAdminLinks(): void
    {
        $this->assertSelectorExists('ul.nav .nav-item .nav-link:contains("Admin")', 'Le lien "Admin" devrait être visible pour les utilisateurs administrateurs');
        $this->assertSelectorExists('ul.nav .nav-item .nav-link:contains("Déconnexion")', 'Le lien "Déconnexion" devrait être visible pour les utilisateurs administrateurs');
    }

    /**
     * Fonction pour vérifier l'absence des liens "Admin" et "Déconnexion"
     */
    private function cannotSeeAdminLinks(): void
    {
        $this->assertSelectorNotExists('ul.nav .nav-item .nav-link:contains("Admin")', 'Le lien "Admin" ne devrait pas être visible pour les utilisateurs non administrateurs');
        $this->assertSelectorNotExists('ul.nav .nav-item .nav-link:contains("Déconnexion")', 'Le lien "Déconnexion" ne devrait pas être visible pour les utilisateurs non administrateurs');
    }
}
