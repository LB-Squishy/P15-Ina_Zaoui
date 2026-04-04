<?php

namespace App\Tests\Functional\Front;

use App\Repository\MediaRepository;
use App\Repository\UserRepository;
use App\Tests\Functional\FunctionalTestCase;

class GuestControllerTest extends FunctionalTestCase
{
    /**
     * Test de l'affichage de la page Invités.
     */
    public function testShowGuestsPage(): void
    {
        $this->get('/guests');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h3', 'Invités');
    }

    /**
     * Test de 404 si l'id de l'invité est invalide.
     */
    public function testReturn404IfBadGuestId(): void
    {
        $this->get('/guest/9999999');
        $this->assertResponseStatusCodeSame(404, 'La page d\'un invité avec un id invalide doit renvoyer une erreur 404');
    }

    /**
     * Test de 404 si l'invité a son accès désactivé ('admin' => false).
     */
    public function testReturn404IfInactiveGuestId(): void
    {
        // Récupère l'id d'un invité inactif depuis la BDD
        $inactiveGuest = $this
            ->service(UserRepository::class)
            ->findOneBy(['super_admin' => false, 'admin' => false]);
        $this->assertNotNull($inactiveGuest, 'Aucun invité inactif trouvé en BDD');
        $inactiveGuestId = $inactiveGuest->getId();

        // Envoi vers la page de l'invité inactif
        $this->get('/guest/' . $inactiveGuestId);
        $this->assertResponseStatusCodeSame(404, 'La page d\'un invité avec un accès désactivé doit renvoyer une erreur 404');
    }

    /**
     * Test de 404 si on essai d'accéder à l'id d'un super_admin sur la page des invités ('super_admin' => true).
     */
    public function testReturn404IfSuperAdminId(): void
    {
        // Récupère l'id du super_admin depuis la BDD
        $superAdmin = $this
            ->service(UserRepository::class)
            ->findOneBy(['super_admin' => true]);
        $this->assertNotNull($superAdmin, 'Aucun super_admin trouvé en BDD');
        $superAdminId = $superAdmin->getId();

        // Envoi vers la page du super_admin
        $this->get('/guest/' . $superAdminId);
        $this->assertResponseStatusCodeSame(404, 'La page d\'un super_admin doit renvoyer une erreur 404');
    }

    /**
     * Test de l'affichage de la liste des invités sur la page /guests.
     */
    public function testShouldListGuests(): void
    {
        $crawler = $this->get('/guests');
        $this->assertResponseIsSuccessful();

        // Récupère les noms des invités et nombre de medias affichés et vérifie qu'il y ai au moins un invité
        $infoGuests = $crawler->filter('div.guest h4');
        $this->assertGreaterThan(0, $infoGuests->count(), 'Aucun invités trouvé dans la page Invités');

        $guestNames = [];
        $guestMediasCount = [];
        foreach ($infoGuests as $infoGuest) {
            $guestText = $infoGuest->textContent;
            // dd($guestText);
            [$guestName, $mediasCount] = explode(' (', $guestText);
            $guestNames[] = $guestName;
            $guestMediasCount[] = (int) str_replace(')', '', $mediasCount);
        }

        // Récupère le nom des invités et le nombre de medias attendus par invités depuis la BDD
        $expectedGuests = $this
            ->service(UserRepository::class)
            ->findBy(['super_admin' => false, 'admin' => true], ['id' => 'ASC'], 5, 0);

        $expectedGuestsNames = [];
        $expectedGuestsMediasCount = [];
        foreach ($expectedGuests as $expectedGuest) {
            $expectedGuestsNames[] = $expectedGuest->getName();
            $expectedGuestsMediasCount[] = $expectedGuest->getMedias()->count();
        }

        $this->assertSame($expectedGuestsNames, $guestNames, 'Le nom des invités affichés ne correspond pas au nom des invités attendus.');
        $this->assertSame($expectedGuestsMediasCount, $guestMediasCount, 'La quantité de media des invités affichés ne correspond pas à la quantité de medias attendus.');
    }

    /**
     * Test de l'affichage de la page d'un invité sur la page /guest/{id}
     * - de la correspondance du nom de l'invité affiché avec le nom attendu
     * - de la correspondance de la description de l'invité affiché avec la description attendue
     * - de la correspondance des chemins des médias de l'invité affichés avec les chemins des médias de l'invité attendu.
     */
    public function testShouldShowGuest(): void
    {
        $crawler = $this->get('/guests');
        $this->assertResponseIsSuccessful();

        // Récupère les liens des invités affichés et vérifie qu'il y en a au moins un
        $links = $crawler->filter('div.guest a[href^="/guest/"]');
        $this->assertGreaterThan(0, $links->count(), 'Aucun invités trouvé dans la page Invités');

        foreach ($links as $link) {
            /** @var \DOMElement $link */
            $clickedLink = $link->getAttribute('href');

            // Envoi vers la page de l'invité correspondant au lien
            $guestCrawler = $this->get($clickedLink);
            $this->assertResponseIsSuccessful();

            // Récupère l'id de l'invité affiché
            $guestId = (int) str_replace('/guest/', '', $clickedLink);
            // Récupère l'invité attendu depuis la BDD
            $expectedGuest = $this
                ->service(UserRepository::class)
                ->findOneBy(['id' => $guestId, 'super_admin' => false, 'admin' => true]);

            // Vérifie que le nom de l'invité affiché correspond au nom de l'invité attendu
            $guestName = $guestCrawler->filter('h3')->text();
            $expectedGuestName = $expectedGuest->getName();

            $this->assertSame($expectedGuestName, $guestName, 'Le nom de l\'invité affiché ne correspond pas au nom de l\'invité attendu.');

            // Vérifie que la description de l'invité affiché correspond à la description de l'invité attendu
            $guestDescription = $guestCrawler->filter('div.col-12 p.mb-5')->text();
            $expectedGuestDescription = $expectedGuest->getDescription();

            $this->assertSame($expectedGuestDescription, $guestDescription, 'La description de l\'invité affichée ne correspond pas à la description de l\'invité attendu.');

            // Vérifie que les chemins des médias de l'invité affichés correspondent aux chemins des médias de l'invité attendu
            $guestMediasPaths = [];
            foreach ($guestCrawler->filter('.media img') as $media) {
                /* @var \DOMElement $media */
                $guestMediasPaths[] = ltrim($media->getAttribute('src'), '/');
            }
            $expectedGuestMediasItems = $this
                ->service(MediaRepository::class)
                ->findBy(['user' => $expectedGuest], ['id' => 'ASC'], 9, 0);
            $expectedGuestMediasPaths = [];
            foreach ($expectedGuestMediasItems as $expectedGuestMediasItem) {
                $expectedGuestMediasPaths[] = $expectedGuestMediasItem->getPath();
            }
            // dd($expectedGuestMediasPaths);
            $this->assertSame($expectedGuestMediasPaths, $guestMediasPaths, 'Les chemins des médias de l\'invité affichés ne correspondent pas aux chemins des médias de l\'invité attendu.');
        }
    }
}
