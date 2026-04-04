<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DomCrawler\Crawler;

abstract class FunctionalTestCase extends WebTestCase
{
    protected KernelBrowser $client;

    /**
     * Initialise le client HTTP pour les tests fonctionnels.
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->client = static::createClient();
    }

    /**
     * Ferme le client HTTP après chaque test pour libérer les ressources.
     */
    protected function tearDown(): void
    {
        parent::tearDown();
        restore_exception_handler();
        unset($this->client);
    }

    /**
     * Récupère le gestionnaire d'entités pour interagir avec la base de données.
     *
     * @return EntityManagerInterface Le gestionnaire d'entités
     */
    protected function getEntityManager(): EntityManagerInterface
    {
        return $this->service(EntityManagerInterface::class);
    }

    protected function service(string $id): object
    {
        return $this->client->getContainer()->get($id);
    }

    /**
     * Effectue une requête GET vers l'URI spécifiée avec les paramètres optionnels.
     *
     * @param string               $uri        L'URI de la requête
     * @param array<string, mixed> $parameters Les paramètres de la requête
     *
     * @return Crawler Le crawler résultant de la requête
     */
    protected function get(string $uri, array $parameters = []): Crawler
    {
        return $this->client->request('GET', $uri, $parameters);
    }

    /**
     * Connecte un utilisateur pour les tests fonctionnels.
     *
     * @param string $email L'email de l'utilisateur à connecter
     */
    protected function loginUser(string $email): User
    {
        $user = $this->service(EntityManagerInterface::class)->getRepository(User::class)->findOneByEmail($email);
        $this->client->loginUser($user);

        return $user;
    }

    /**
     * Connecte un super administrateur pour les tests fonctionnels.
     */
    protected function loginAsSuperAdmin(): User
    {
        return $this->loginUser('ina@zaoui.com');
    }

    /**
     * Connecte un administrateur pour les tests fonctionnels.
     */
    protected function loginAsAdmin(): User
    {
        return $this->loginUser('invite+2@example.com');
    }

    /**
     * Connecte un utilisateur pour les tests fonctionnels.
     */
    protected function loginAsUser(): User
    {
        return $this->loginUser('invite+1@example.com');
    }

    /**
     * Soumet un formulaire en utilisant le texte du bouton et les données du formulaire.
     *
     * @param string               $buttonText Le texte du bouton de soumission du formulaire
     * @param array<string, mixed> $formData   Les données à soumettre dans le formulaire
     * @param string               $methode    La méthode HTTP à utiliser pour la soumission (par défaut 'POST')
     *
     * @return Crawler Le crawler résultant de la soumission du formulaire
     */
    protected function submitForm(string $buttonText, array $formData, string $methode = 'POST'): Crawler
    {
        return $this->client->submitForm($buttonText, $formData, $methode);
    }
}
