<?php

namespace App\Tests\Functional\Admin;

use App\Repository\AlbumRepository;
use App\Tests\Functional\FunctionalTestCase;

class AlbumControllerTest extends FunctionalTestCase
{
    // //////////////////////////////////////////////////////////////////-----TEST D'AFFICHAGE DE LA PAGE /ADMIN/ALBUM-----////////////////////////////////////////////////////////////////////

    /**
     * Test de l'affichage de la page /admin/album pour un super admin.
     */
    public function testShowAdminAlbumPageForSuperAdmin(): void
    {
        $this->loginAsSuperAdmin();
        $crawler = $this->get('/admin/album');
        $this->assertResponseIsSuccessful();

        // Récupère les albums affichés dans la page
        $albums = $crawler->filter('table tbody tr td:first-child');
        $this->assertGreaterThan(0, $albums->count(), 'Aucun album trouvé dans la page album');

        // Récupère les noms des albums affichés dans la page
        $albumNames = [];
        foreach ($albums as $album) {
            $albumNames[] = $album->textContent;
        }

        // Récupère les albums attendus en BDD
        $expectedAlbum = $this
            ->service(AlbumRepository::class)
            ->findAll();

        // Récupère les noms des albums attendus en BDD
        $expectedAlbumNames = [];
        foreach ($expectedAlbum as $album) {
            $expectedAlbumNames[] = $album->getName();
        }

        $this->assertSame($expectedAlbumNames, $albumNames, 'Le nom des albums affichés ne correspond pas au nom des albums attendus.');
    }

    /**
     * Test de refus d'accès à la page /admin/album pour un admin.
     */
    public function testIfAccessDeniedForAdmin(): void
    {
        $this->loginAsAdmin();
        $this->get('/admin/album');
        $this->assertResponseStatusCodeSame(403);
    }

    /**
     * Test de refus d'accès à la page /admin/album pour un utilisateur désactivé.
     */
    public function testIfAccessDeniedForUser(): void
    {
        $this->loginAsUser();
        $this->get('/admin/album');
        $this->assertResponseStatusCodeSame(403);
    }

    // //////////////////////////////////////////////////////////////////-----TEST D'AJOUT D'UN ALBUM-----////////////////////////////////////////////////////////////////////

    /**
     * Test de l'ajout d'un album par un super admin.
     */
    public function testAddAlbumAsSuperAdmin(): void
    {
        $this->loginAsSuperAdmin();
        $this->get('/admin/album/add');
        $this->assertResponseIsSuccessful();

        // Soumet le formulaire d'ajout d'album
        $this->submitForm('Ajouter', [
            'album[name]' => 'Test Album 9999',
        ]);
        $this->assertResponseRedirects('/admin/album');

        // Vérifie que l'album a été ajouté en base de données
        $expectedAlbum = $this
            ->service(AlbumRepository::class)
            ->findAll();
        $expectedAlbumNames = [];
        foreach ($expectedAlbum as $album) {
            $expectedAlbumNames[] = $album->getName();
        }
        $this->assertContains('Test Album 9999', $expectedAlbumNames, 'L\'album n\'a pas été ajouté en base de données.');

        // Suivre la redirection et vérifier que l'album ajouté est affiché dans la liste des albums
        $albumCrawler = $this->client->followRedirect();
        $albums = $albumCrawler->filter('table tbody tr td:first-child');
        $albumNames = [];
        foreach ($albums as $album) {
            $albumNames[] = $album->textContent;
        }
        $this->assertContains('Test Album 9999', $albumNames, 'L\'album ajouté n\'est pas affiché dans la liste des albums.');
    }

    // /////////////////////////////////////////////////////////////////-----TEST DE MISE À JOUR D'UN ALBUM-----////////////////////////////////////////////////////////////////////
    /**
     * Test de la mise à jour d'un album par un super admin.
     */
    public function testUpdateAlbumAsSuperAdmin(): void
    {
        $this->loginAsSuperAdmin();
        $this->get('/admin/album/update/1');
        $this->assertResponseIsSuccessful();

        // Soumet le formulaire de mise à jour d'album
        $this->submitForm('Modifier', [
            'album[name]' => 'Test Album Updated',
        ]);
        $this->assertResponseRedirects('/admin/album');

        // Vérifie que l'album a été mis à jour en base de données
        $expectedAlbum = $this
            ->service(AlbumRepository::class)
            ->findAll();
        $expectedAlbumNames = [];
        foreach ($expectedAlbum as $album) {
            $expectedAlbumNames[] = $album->getName();
        }
        $this->assertContains('Test Album Updated', $expectedAlbumNames, 'L\'album n\'a pas été mis à jour en base de données.');

        // Suivre la redirection et vérifier que l'album ajouté est affiché dans la liste des albums
        $albumCrawler = $this->client->followRedirect();
        $albums = $albumCrawler->filter('table tbody tr td:first-child');
        $albumNames = [];
        foreach ($albums as $album) {
            $albumNames[] = $album->textContent;
        }
        $this->assertContains('Test Album Updated', $albumNames, 'L\'album ajouté n\'est pas affiché dans la liste des albums.');
    }

    // //////////////////////////////////////////////////////////////////-----TEST DE SUPPRESSION D'UN ALBUM-----////////////////////////////////////////////////////////////////////

    /**
     * Test de la suppression d'un album sans media par un super admin
     * - Test de suppression de l'album 6 qui n'est pas associé à un média.
     */
    public function testDeleteAlbumAsSuperAdmin(): void
    {
        $this->loginAsSuperAdmin();
        $this->get('/admin/album/delete/6');
        $this->assertResponseRedirects('/admin/album');

        // Vérifie que l'album a été supprimé en base de données
        $expectedAlbum = $this
            ->service(AlbumRepository::class)
            ->findAll();
        $expectedAlbumNames = [];
        foreach ($expectedAlbum as $album) {
            $expectedAlbumNames[] = $album->getName();
        }
        $this->assertNotContains('Album 6', $expectedAlbumNames, 'L\'album n\'a pas été supprimé en base de données.');

        // Suivre la redirection et vérifier que l'album supprimé n'est plus affiché dans la liste des albums
        $albumCrawler = $this->client->followRedirect();
        $albums = $albumCrawler->filter('table tbody tr td:first-child');
        $albumNames = [];
        foreach ($albums as $album) {
            $albumNames[] = $album->textContent;
        }
        $this->assertNotContains('Album 6', $albumNames, 'L\'album supprimé est toujours affiché dans la liste des albums.');
    }
}
