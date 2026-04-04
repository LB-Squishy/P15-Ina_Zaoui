<?php

namespace App\Tests\Functional\Admin;

use App\Entity\User;
use App\Repository\MediaRepository;
use App\Tests\Functional\FunctionalTestCase;
use Symfony\Component\DomCrawler\Crawler;

class MediaControllerTest extends FunctionalTestCase
{
    // //////////////////////////////////////////////////////////////////-----TEST D'AFFICHAGE DE LA PAGE /ADMIN/MEDIA-----////////////////////////////////////////////////////////////////////
    /**
     * Test de l'affichage de la page /admin/media pour un super admin.
     */
    public function testShowAdminMediaPageForSuperAdmin(): void
    {
        $user = $this->loginAsSuperAdmin();
        $crawler = $this->get('/admin/media');

        $this->assertResponseIsSuccessful();

        $this->checkMediaPageCount($user, $crawler);
    }

    /**
     * Test de l'affichage de la page /admin/media pour un admin.
     */
    public function testShowAdminMediaPageForAdmin(): void
    {
        $user = $this->loginAsAdmin();
        $crawler = $this->get('/admin/media');

        $this->assertResponseIsSuccessful();

        $this->checkMediaPageCount($user, $crawler);
    }

    /**
     * Vérifie que le nombre de pages de médias affichées correspond au nombre attendu en fonction du rôle de l'utilisateur.
     */
    private function checkMediaPageCount(User $user, Crawler $crawler): void
    {
        // Prépare les critères de recherche en fonction du rôle de l'utilisateur
        $criteria = [];
        if (!$user->isSuperAdmin()) {
            $criteria['user'] = $user;
        }

        // Vérifie la quantité de pages attendue en BDD
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
        if ("Dernière page" !== $lastPageLink->text()) {
            $mediaPageCount = 1;
        } else {
            $mediaPageCount = (int) str_replace('/admin/media?page=', '', $lastPageLink->attr('href'));
        }

        $this->assertSame($expectedMediaPageCount, $mediaPageCount, 'Le nombre de pages de médias affichées ne correspond pas au nombre attendu.');
    }

    // //////////////////////////////////////////////////////////////////-----TEST D'AFFICHAGE DE LA PAGE /ADMIN/MEDIA/ADD-----////////////////////////////////////////////////////////////////////
    /**
     * Test de l'affichage de la page /admin/media/add pour un super admin.
     */
    public function testShowGoodAdminMediaAddPageForSuperAdmin(): void
    {
        $this->loginAsSuperAdmin();
        $this->get('/admin/media/add');

        $this->assertResponseIsSuccessful();

        $this->assertSelectorExists('select[name="media[user]"]', 'Le champ "user" devrait être visible pour les super admin');
        $this->assertSelectorExists('select[name="media[album]"]', 'Le champ "album" devrait être visible pour les super admin');
        $this->assertSelectorExists('input[name="media[title]"]', 'Le champ "title" devrait être visible pour les super admin');
        $this->assertSelectorExists('input[name="media[file]"]', 'Le champ "file" devrait être visible pour les super admin');
    }

    /**
     * Test de l'affichage de la page /admin/media/add pour un admin.
     */
    public function testShowGoodAdminMediaAddPageForAdmin(): void
    {
        $this->loginAsAdmin();
        $this->get('/admin/media/add');

        $this->assertResponseIsSuccessful();

        $this->assertSelectorNotExists('select[name="media[user]"]', 'Le champ "user" ne devrait pas être visible pour les admin');
        $this->assertSelectorNotExists('select[name="media[album]"]', 'Le champ "album" ne devrait pas être visible pour les admin');
        $this->assertSelectorExists('input[name="media[title]"]', 'Le champ "title" devrait être visible pour les admin');
        $this->assertSelectorExists('input[name="media[file]"]', 'Le champ "file" devrait être visible pour les admin');
    }

    // //////////////////////////////////////////////////////////////////-----TEST D'AJOUT DE MEDIA-----////////////////////////////////////////////////////////////////////

    /**
     * Test d'ajout de media par un super admin.
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
     * Test d'ajout de media par un admin.
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
     * Ajout d'un média en fonction du rôle de l'utilisateur connecté.
     */
    private function addMediaByUserTest(User $user): void
    {
        $media = null;
        $imagePath = null;

        // Founir le chemin absolu du dossier uploads pour pouvoir nettoyer les fichiers après le test
        $projectDirectory = $this->client->getContainer()->getParameter('kernel.project_dir');
        $uploadsDirectory = $projectDirectory . '/public/';

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
            $this->assertFileExists($uploadsDirectory . $media->getPath(), 'Le fichier du média ajouté n\'a pas été trouvé dans le dossier uploads.');
        } finally {
            // Nettoyer le dossier uploads après le test
            if ($media) {
                if (file_exists($uploadsDirectory . $media->getPath())) {
                    unlink($uploadsDirectory . $media->getPath());
                }
            }
        }
    }

    // //////////////////////////////////////////////////////////////////-----TEST D'AJOUT DE MEDIA-----////////////////////////////////////////////////////////////////////

    /**
     * Test d'ajout de media dépassant les 2 Mo.
     */
    public function testCantAddMediaMoreThan2Mo(): void
    {
        $this->loginAsSuperAdmin();
        $this->get('/admin/media/add');

        $this->assertResponseIsSuccessful();

        $media = null;
        $imagePath = null;

        // Fournir le chemin de l'image de test
        $imagePath = __DIR__ . '/../../Images/image-more-than-2mo.jpg';

        // Soumettre le formulaire d'ajout de média
        $this->submitForm('Ajouter', [
            'media[user]' => 1,
            'media[album]' => 1,
            'media[title]' => 'Test image 2mo',
            'media[file]' => $imagePath,
        ]);
        $this->assertResponseIsSuccessful();

        // Vérifier que le bon message d'erreur est affiché
        $this->assertSelectorTextContains('.invalid-feedback', 'Sa taille ne doit pas dépasser 2 MB.', 'Le message d\'erreur correspondant n\'est pas affiché.');

        // Vérifier que le média n'a pas été ajouté en BDD
        $media = $this
            ->service(MediaRepository::class)
            ->findOneBy(['title' => 'Test image 2mo']);
        $this->assertNull($media, 'Le média a été trouvé en BDD alors qu\'il ne devrait pas l\'être.');
    }

    /**
     * Test d'ajout de media qui n'est pas une image.
     */
    public function testCantAddMediaThatIsNotAnImage(): void
    {
        $this->loginAsSuperAdmin();
        $this->get('/admin/media/add');

        $this->assertResponseIsSuccessful();

        $media = null;
        $imagePath = null;

        // Fournir le chemin de l'image de test
        $imagePath = __DIR__ . '/../../Images/file-is-not-an-img.txt';

        // Soumettre le formulaire d'ajout de média
        $this->submitForm('Ajouter', [
            'media[user]' => 1,
            'media[album]' => 1,
            'media[title]' => 'Test fichier txt',
            'media[file]' => $imagePath,
        ]);
        $this->assertResponseIsSuccessful();

        // Vérifier que le bon message d'erreur est affiché
        $this->assertSelectorTextContains('.invalid-feedback', 'Les types autorisés sont "image/jpeg", "image/png", "image/webp"', 'Le message d\'erreur correspondant n\'est pas affiché.');

        // Vérifier que le média n'a pas été ajouté en BDD
        $media = $this
            ->service(MediaRepository::class)
            ->findOneBy(['title' => 'Test fichier txt']);
        $this->assertNull($media, 'Le média a été trouvé en BDD alors qu\'il ne devrait pas l\'être.');
    }

    // //////////////////////////////////////////////////////////////////-----TEST DE SUPPRESSION DE MEDIA-----////////////////////////////////////////////////////////////////////

    /**
     * Test de la suppression d'un média qui n'appartient pas à l'utilisateur connecté par un admin.
     */
    public function testCantDeleteMediaOfOtherIfNotSuperAdmin(): void
    {
        // Connect l'admin de test qui est invite+2@example.com
        $this->loginAsAdmin();

        // Essai de supprimer le media qui a pour id 295 et qui appartient à invite+5@example.com
        $this->get('/admin/media/delete/295');

        // Vérifie que la suppression est interdite et que le code de réponse est 403
        $this->assertResponseStatusCodeSame(403);
    }

    /**
     * Test de la suppression d'un média qui n'appartient pas à l'utilisateur connecté par un super admin.
     */
    public function testCanDeleteMediaAsSuperAdmin(): void
    {
        // Connect l'admin de test qui est invite+2@example.com
        $this->loginAsSuperAdmin();

        // Essai de supprimer le media qui a pour id 295 et qui appartient à invite+5@example.com
        $this->get('/admin/media/delete/295');

        // Vérifie que la suppression a eu lieu
        $deletedMedia = $this
            ->service(MediaRepository::class)
            ->findOneBy(['id' => 295]);
        $this->assertNull($deletedMedia, 'Le média n\'a pas été supprimé.');
    }
}
