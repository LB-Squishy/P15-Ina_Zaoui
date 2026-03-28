<?php

namespace App\Tests\Functional\Admin;

use App\Tests\Functional\FunctionalTestCase;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class SecurityControllerTest extends FunctionalTestCase
{
    /**
     * Test de l'affichage de la page de connexion
     */
    public function testShowLoginPage(): void
    {
        $this->get('/login');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Connexion');
    }

    /**
     * Test de la tentative de connexion avec des identifiants incorrects
     */
    public function testWithBadLoginShouldFail(): void
    {
        $this->get('/login');

        $this->client->submitForm('Connexion', [
            '_username' => 'invite+1@example.com',
            '_password' => 'mauvais mot de passe',
        ]);
        $this->assertResponseRedirects('/login');

        // Suivre la redirection et vérifier que la présence du message d'erreur
        $this->client->followRedirect();
        $this->assertSelectorTextContains('div.alert-danger', 'Identifiants invalides.');

        // Vérifie que l'utilisateur n'est pas connecté en vérifiant les rôles
        $authorizationChecker = $this->service(AuthorizationCheckerInterface::class);
        self::assertFalse($authorizationChecker->isGranted('ROLE_USER'));
        self::assertFalse($authorizationChecker->isGranted('ROLE_ADMIN'));
        self::assertFalse($authorizationChecker->isGranted('ROLE_SUPER_ADMIN'));

        // Vérifie que l'utilisateur n'est pas connecté en essayant d'accéder à une page protégée
        $this->get('/admin/media');
        $this->assertResponseRedirects('/login');
    }

    /**
     * Test de la tentative de connexion en tant que super admin
     */
    public function testIfLoginAsSuperAdminIsSuccessful(): void
    {
        $this->get('/login');

        $this->client->submitForm('Connexion', [
            '_username' => 'ina@zaoui.com',
            '_password' => 'password',
        ]);
        $this->assertResponseRedirects('/admin/media');

        // Suivre la redirection et vérifier que la présence des liens accessibles
        $this->client->followRedirect();
        $this->assertSelectorExists('a[href="/admin/media"]', 'Le lien vers la page des medias doit être présent pour un super_admin');
        $this->assertSelectorExists('a[href="/admin/album"]', 'Le lien vers la page des albums doit être présent pour un super_admin');
        $this->assertSelectorExists('a[href="/admin/guest"]', 'Le lien vers la page des invités doit être présent pour un super_admin');

        // Vérifie la présence du rôle de super admin
        $authorizationChecker = $this->service(AuthorizationCheckerInterface::class);
        self::assertTrue($authorizationChecker->isGranted('ROLE_SUPER_ADMIN'));

        // Vérifie que l'utilisateur est bien déconnecté en essayant d'accéder à une page protégée
        $this->get('/logout');
        $this->assertResponseRedirects('/');
        $this->get('/admin/guest');
        $this->assertResponseRedirects('/login');
    }

    /**
     * Test de la tentative de connexion en tant qu'admin
     */
    public function testIfLoginAsAdminIsSuccessful(): void
    {
        $this->get('/login');

        $this->client->submitForm('Connexion', [
            '_username' => 'invite+2@example.com',
            '_password' => 'password',
        ]);
        $this->assertResponseRedirects('/admin/media');

        // Suivre la redirection et vérifier que la présence des liens accessibles
        $this->client->followRedirect();
        $this->assertSelectorExists('a[href="/admin/media"]', 'Le lien vers la page des medias doit être présent pour un admin');
        $this->assertSelectorNotExists('a[href="/admin/album"]', 'Le lien vers la page des albums ne doit pas être présent pour un admin');
        $this->assertSelectorNotExists('a[href="/admin/guest"]', 'Le lien vers la page des invités ne doit pas être présent pour un admin');

        // Vérifie la présence des bons rôles pour un admin
        $authorizationChecker = $this->service(AuthorizationCheckerInterface::class);
        self::assertFalse($authorizationChecker->isGranted('ROLE_SUPER_ADMIN'));
        self::assertTrue($authorizationChecker->isGranted('ROLE_ADMIN'));

        // Vérifie que l'utilisateur est bien déconnecté en essayant d'accéder à une page protégée
        $this->get('/logout');
        $this->assertResponseRedirects('/');
        $this->get('/admin/media');
        $this->assertResponseRedirects('/login');
    }

    /**
     * Test de la tentative de connexion en tant qu'utilisateur désactivé
     */
    public function testIfAccessIsDeniedForDisabledUser(): void
    {
        $this->get('/login');

        $this->client->submitForm('Connexion', [
            '_username' => 'invite+1@example.com',
            '_password' => 'password',
        ]);
        $this->assertResponseRedirects('/admin/media');

        // Suivre la redirection et vérifier le retour d'une erreur 403
        $this->client->followRedirect();
        $this->assertResponseStatusCodeSame(403, 'La tentative de connexion d\'un utilisateur désactivé doit renvoyer une erreur 403');

        // Vérifie la présence des bons rôles pour un utilisateur désactivé
        $authorizationChecker = $this->service(AuthorizationCheckerInterface::class);
        self::assertFalse($authorizationChecker->isGranted('ROLE_SUPER_ADMIN'));
        self::assertFalse($authorizationChecker->isGranted('ROLE_ADMIN'));
        self::assertTrue($authorizationChecker->isGranted('ROLE_USER'));

        // Vérifie que l'utilisateur est bien déconnecté en essayant d'accéder à une page protégée
        $this->get('/logout');
        $this->assertResponseRedirects('/');
        $this->get('/admin/media');
        $this->assertResponseRedirects('/login');
    }
}
