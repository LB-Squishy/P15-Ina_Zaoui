<?php

namespace App\Tests\Functional\Admin;

use App\Repository\MediaRepository;
use App\Tests\Functional\FunctionalTestCase;

class MediaControllerTest extends FunctionalTestCase
{
    ////////////////////////////////////////////////////////////////////-----TEST D'AFFICHAGE DE LA PAGE /ADMIN/MEDIA-----////////////////////////////////////////////////////////////////////
    /**
     * Test de l'affichage de la page /admin/media pour un super admin
     */
    public function testShowAdminMediaPageForSuperAdmin(): void
    {
        $user = $this->loginAsSuperAdmin();
        $crawler = $this->get('/admin/media');

        $this->assertResponseIsSuccessful();

        $this->checkMediaPageCount($user, $crawler);
    }

    /** 
     * Test de l'affichage de la page /admin/media pour un admin
     */
    public function testShowAdminMediaPageForAdmin(): void
    {
        $user = $this->loginAsAdmin();
        $crawler = $this->get('/admin/media');

        $this->assertResponseIsSuccessful();

        $this->checkMediaPageCount($user, $crawler);
    }

    /**
     * Vérifie que le nombre de pages de médias affichées correspond au nombre attendu en fonction du rôle de l'utilisateur
     * @param $user l'utilisateur connecté
     * @param $crawler le crawler de la page /admin/media
     * @return void
     */
    public function checkMediaPageCount($user, $crawler): void
    {
        // Prépare les critères de recherche en fonction du rôle de l'utilisateur
        $criteria = [];
        if (!$user->isSuperAdmin()) {
            $criteria['user'] = $user;
        }

        //Vérifie la quantité de pages attendue en BDD
        $expectedTotalMedias = $this
            ->service(MediaRepository::class)
            ->findBy($criteria);
        $expectedTotalMediasCount = count($expectedTotalMedias);
        $expectedMediaPageCount = $expectedTotalMediasCount > 25 ? (int) ceil($expectedTotalMediasCount / 25) : 1;
        // dd($expectedMediaPageCount);

        // Vérifie la quantité de pages affichées
        $mediaPageCount = 0;
        $lastPageLink = $crawler
            ->filter('ul.pagination a.page-link')
            ->last();
        if ($lastPageLink->text() !== "Dernière page") {
            $mediaPageCount = 1;
        } else {
            $mediaPageCount = (int) str_replace('/admin/media?page=', '', $lastPageLink->attr('href'));
        }

        $this->assertSame($expectedMediaPageCount, $mediaPageCount, 'Le nombre de pages de médias affichées ne correspond pas au nombre attendu.');
    }

    ////////////////////////////////////////////////////////////////////-----TEST D'AJOUT DE MEDIA-----////////////////////////////////////////////////////////////////////

    /**
     * Test d'ajout de media par un super admin
     */
    public function testAddMediaAsSuperAdmin(): void
    {
        $user = $this->loginAsSuperAdmin();
        $crawler = $this->get('/admin/media');

        $this->assertResponseIsSuccessful();

        // Se rendre sur la page d'ajout de média
        $link = $crawler->selectLink("Ajouter")->link();
        $this->client->click($link);
        $this->assertResponseIsSuccessful();

        $this->addMediaByUserTest($user);
    }

    /** 
     * Test d'ajout de media par un admin
     */
    public function testAddMediaAsAdmin(): void
    {
        $user = $this->loginAsAdmin();
        $crawler = $this->get('/admin/media');

        $this->assertResponseIsSuccessful();

        // Se rendre sur la page d'ajout de média
        $link = $crawler->selectLink("Ajouter")->link();
        $this->client->click($link);
        $this->assertResponseIsSuccessful();

        $this->addMediaByUserTest($user);
    }

    /**
     * Ajout d'un média en fonction du rôle de l'utilisateur connecté
     * @param $user l'utilisateur connecté
     * @return void
     */
    public function addMediaByUserTest($user): void
    {
        $media = null;
        $imagePath = null;

        try {
            // Fournir le chemin de l'image de test
            $imagePath = __DIR__ . '/../../Images/image-test-ok.webp';

            // Soumettre le formulaire d'ajout de média
            if ($user->isSuperAdmin()) {
                $this->submitForm('Ajouter', [
                    'media[user]' => 1,
                    'media[album]' => 1,
                    'media[title]' => 'Test image OK',
                    'media[file]' => $imagePath,
                ]);
            } else {
                $this->submitForm('Ajouter', [
                    'media[title]' => 'Test image OK',
                    'media[file]' => $imagePath,
                ]);
            }
            $this->assertResponseRedirects('/admin/media');

            // Vérifier que le média a bien été ajouté en BDD
            $media = $this
                ->service(MediaRepository::class)
                ->findOneBy(['title' => 'Test image OK']);
            $this->assertNotNull($media, 'Le média n\'a pas été trouvé en BDD après l\'ajout.');

            if ($user->isSuperAdmin()) {
                $this->assertSame(1, $media->getAlbum()->getId(), 'Le média ajouté n\'est pas associé au bon album.');
            }
            $this->assertFileExists($media->getPath(), 'Le fichier du média ajouté n\'a pas été trouvé dans le dossier uploads.');
        } finally {
            // Nettoyer le dossier uploads après le test
            if ($media && file_exists($media->getPath())) {
                unlink($media->getPath());
            }
        }
    }

    ////////////////////////////////////////////////////////////////////-----TEST DE SUPPRESSION DE MEDIA-----////////////////////////////////////////////////////////////////////

    public function testCantDeleteMediaOfOtherIfNotSuperAdmin(): void
    {
        // Connect l'admin de test qui est invite+2@example.com
        $this->loginAsAdmin();

        //Essai de supprimer le media qui a pour id 295 et qui appartient à invite+5@example.com
        $this->get('/admin/media/delete/295');

        // Vérifie que la suppression est interdite et que le code de réponse est 403
        $this->assertResponseStatusCodeSame(403);
    }
}
