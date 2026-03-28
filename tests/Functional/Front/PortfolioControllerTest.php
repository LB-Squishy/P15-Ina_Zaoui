<?php

namespace App\Tests\Functional\Front;

use App\Repository\AlbumRepository;
use App\Repository\MediaRepository;
use App\Repository\UserRepository;
use App\Tests\Functional\FunctionalTestCase;

class PortfolioControllerTest extends FunctionalTestCase
{
    /**
     * Test de l'affichage de la page portfolio
     */
    public function testShowPortfolioPage(): void
    {
        $this->get('/portfolio');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h3', 'Portfolio');
    }

    /**
     * Test de l'affichage de la liste des albums
     */
    public function testShouldListAlbums(): void
    {
        $crawler = $this->get('/portfolio');
        $this->assertResponseIsSuccessful();

        // Récupère les liens d'album affichés
        $albums = $crawler->filter('div.mb-5 a.btn[href^="/portfolio/"]');
        $this->assertGreaterThan(0, $albums->count(), 'Aucun album trouvé dans la page portfolio');

        // Récupère les albums attendus depuis la base de données
        $expectedAlbums = $this
            ->service(AlbumRepository::class)
            ->findAll();

        $this->assertSame(count($expectedAlbums), $albums->count(), 'Le nombre d\'albums affichés dans le portfolio ne correspond pas au nombre attendu.');
    }

    /**
     * Test de l'affichage de tout les medias sur la page /portfolio (bouton "Toutes")
     */
    public function testShouldListAllMedias(): void
    {
        $crawler = $this->get('/portfolio');
        $this->assertResponseIsSuccessful();

        // Récupère les medias affichés
        $medias = $crawler->filter('.media img');
        $this->assertGreaterThan(0, $medias->count(), 'Aucun media trouvé dans la page portfolio');

        // Récupère les medias attendus depuis la base de données
        $ina = $this
            ->service(UserRepository::class)
            ->findOneBy(['super_admin' => true]);
        $expectedMedias = $this
            ->service(MediaRepository::class)
            ->findBy(['user' => $ina]);

        $this->assertSame(count($expectedMedias), $medias->count(), 'Le nombre d\'images affichées dans le portfolio ne correspond pas au nombre attendu.');
    }

    /**
     * Test de renvoi à /portfolio si l'id de l'album est invalide
     */
    public function testBackToAllMediasIfBadAlbumId(): void
    {
        $crawler = $this->get('/portfolio/9999999');
        $this->assertResponseIsSuccessful();

        // Récupère les medias affichés
        $medias = $crawler->filter('.media img');
        $this->assertGreaterThan(0, $medias->count(), 'Aucun media trouvé dans la page portfolio');

        // Récupère les medias attendus depuis la base de données
        $ina = $this
            ->service(UserRepository::class)
            ->findOneBy(['super_admin' => true]);
        $expectedMedias = $this
            ->service(MediaRepository::class)
            ->findBy(['user' => $ina]);

        $this->assertSame(count($expectedMedias), $medias->count(), 'Le nombre d\'images affichées dans le portfolio ne correspond pas au nombre attendu.');
    }

    /**
     * Test de l'affichage de tout les medias correspondant à un album sur la page /portfolio/{id}
     * - de la correspondance du nombre de médias affichés avec le nombre attendu
     * - de la correspondance des titres des médias affichés avec les titres attendus
     */
    public function testShouldListGoodMediasFromAlbum(): void
    {
        $crawler = $this->get('/portfolio');
        $this->assertResponseIsSuccessful();

        // Récupère les liens d'album affichés et vérifie qu'il y en a au moins un
        $links = $crawler->filter('div.mb-5 a.btn[href^="/portfolio/"]');
        $this->assertGreaterThan(0, $links->count(), 'Aucun album trouvé dans la page portfolio');

        foreach ($links as $link) {
            /** @var \DOMElement $link */
            $href = $link->getAttribute('href');

            // Envoi vers la page de l'album correspondant au lien
            $albumCrawler = $this->get($href);
            $this->assertResponseIsSuccessful();

            // Récupère l'id de l'album à partir du lien et trouve l'album coorrespondant en BDD
            $albumId = (int) str_replace('/portfolio/', '', $href);
            $album = $this->service(AlbumRepository::class)->find($albumId);

            // Récupère les médias attendus et affichés pour l'album
            $expectedMedias = $this
                ->service(MediaRepository::class)
                ->findBy(['album' => $album]);
            $medias = $albumCrawler
                ->filter('.media img');

            $this->assertSame(count($expectedMedias), $medias->count(), 'Le nombre d\'images affichées pour l\'album ' . $album->getName() . ' ne correspond pas au nombre attendu.');

            // Récupère les titres des médias attendus et affichés
            $expectedTitles = [];
            foreach ($expectedMedias as $media) {
                $expectedTitles[] = $media->getTitle();
            }
            $titleElements = $albumCrawler
                ->filter('.media-title');
            $titles = [];
            foreach ($titleElements as $titleElement) {
                $titles[] = $titleElement->textContent;
            }

            $this->assertSame($expectedTitles, $titles, 'Les titres des images affichées pour l\'album ' . $album->getName() . ' ne correspondent pas aux titres attendus.');
        }
    }
}
