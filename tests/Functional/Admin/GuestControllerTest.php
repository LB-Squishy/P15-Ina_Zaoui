<?php

namespace App\Tests\Functional\Admin;

use App\Repository\UserRepository;
use App\Tests\Functional\FunctionalTestCase;

class GuestControllerTest extends FunctionalTestCase
{
    /////////////////////////////////////////////////////////////////////-----TEST D'ACCES À LA PAGE DE GESTION DES INVITÉS-----////////////////////////////////////////////////////////////////////

    /**
     * Test de l'accès à la page /admin/guest pour un super admin
     */
    public function testAccessAdminGuestPageForSuperAdmin(): void
    {
        $this->loginAsSuperAdmin();
        $this->get('/admin/guest');
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

    /////////////////////////////////////////////////////////////////////-----TEST D'AFFICHAGE DE LA PAGE DE GESTION DES INVITÉS-----////////////////////////////////////////////////////////////////////

    /**
     * Test de l'affichage de la page /admin/guest pour un super admin
     */
    public function testGuestPageCount(): void
    {
        $this->loginAsSuperAdmin();
        $crawler = $this->get('/admin/guest');
        $this->assertResponseIsSuccessful();

        // Vérifie la quantité de pages attendue en BDD
        $expectedTotalGuests = $this
            ->service(UserRepository::class)
            ->findBy(['super_admin' => false]);
        $expectedTotalGuestsCount = count($expectedTotalGuests);
        $expectedGuestPageCount = $expectedTotalGuestsCount > 25 ? (int) ceil($expectedTotalGuestsCount / 25) : 1;

        // Vérifie la quantité de pages affichées
        $guestPageCount = 0;
        $lastPageLink = $crawler
            ->filter('ul.pagination a.page-link')
            ->last();
        if ($lastPageLink->text() !== "Dernière page") {
            $guestPageCount = 1;
        } else {
            $guestPageCount = (int) str_replace('/admin/guest?page=', '', $lastPageLink->attr('href'));
        }
        $this->assertSame($expectedGuestPageCount, $guestPageCount, 'Le nombre de pages affichées ne correspond pas au nombre de pages attendu.');
    }

    /////////////////////////////////////////////////////////////////////-----TEST DE REVOCATION DES DROITS D'ACCES D'INVITÉ-----////////////////////////////////////////////////////////////////////

    /**
     * Test de la révocation des droits d'accès d'un invité par un super admin
     */
    public function testRevokeGuestAccessBySuperAdmin(): void
    {
        // Se connecte en tant qu'Invité 2 pour vérifier l'accès à la page /admin/media avant la révocation des droits d'accès
        $this->loginUser('invite+2@example.com');
        $this->get('/admin/media');
        $this->assertResponseIsSuccessful();

        // Se connecte en tant que super admin et accède à la page de gestion des invités
        $this->loginAsSuperAdmin();
        $crawler = $this->get('/admin/guest');
        $this->assertResponseIsSuccessful();

        // Récupère le lien de révocation du deuxième invité de la liste
        $guestLink = $crawler->filter('table tbody tr td:nth-child(3) a')->eq(1);
        $this->assertNotNull($guestLink, 'Le lien de révocation du deuxième invité n\'a pas été trouvé.');

        // Révoque les droits d'accès du deuxième invité de la liste
        $this->client->click($guestLink->link());
        $this->assertResponseRedirects('/admin/guest');

        // Vérifie que le deuxième invité n'a plus les droits d'accès en BDD
        $expectedGuest = $this
            ->service(UserRepository::class)
            ->findOneBy(['name' => 'Invité 2']);
        $this->assertFalse($expectedGuest->getRoles() === ['ROLE_ADMIN'], 'Les droits d\'accès de l\'invité n\'ont pas été révoqués.');

        // Se reconnecte en tant que Invité 2 pour vérifier le retour 403 à l'accès à la page /admin/media
        $this->loginUser('invite+2@example.com');
        $this->get('/admin/media');
        $this->assertResponseStatusCodeSame(403, 'L\'invité a encore accès à la page /admin/media après la révocation de ses droits d\'accès.');
    }

    /////////////////////////////////////////////////////////////////////-----TEST DE SUPPRESSION D'INVITÉ-----////////////////////////////////////////////////////////////////////

    /**
     * Test de la suppression d'un invité par un super admin
     */
    public function testDeleteGuestBySuperAdmin(): void
    {
        $this->loginAsSuperAdmin();
        $crawler = $this->get('/admin/guest');
        $this->assertResponseIsSuccessful();

        // Récupère le lien de suppression du deuxième invité de la liste
        $guestLink = $crawler->filter('table tbody tr td:nth-child(4) a')->eq(1);
        $this->assertNotNull($guestLink, 'Le lien de suppression du deuxième invité n\'a pas été trouvé.');

        // Supprime le deuxième invité de la liste
        $this->client->click($guestLink->link());
        $this->assertResponseRedirects('/admin/guest');

        // Vérifie que le deuxième invité a été supprimé en BDD
        $deletedGuest = $this
            ->service(UserRepository::class)
            ->findOneBy(['name' => 'Invité 2']);
        $this->assertNull($deletedGuest, 'L\'invité n\'a pas été supprimé.');
    }
}
