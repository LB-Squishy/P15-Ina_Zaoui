<?php

namespace App\DataFixtures;

use App\Entity\Album;
use App\Entity\Media;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class MediaTestFixtures extends Fixture implements DependentFixtureInterface, FixtureGroupInterface
{
    private const TEST_IMAGES = [
        'test-uploads/test-0001.webp',
        'test-uploads/test-0002.webp',
        'test-uploads/test-0003.webp',
        'test-uploads/test-0004.webp',
        'test-uploads/test-0005.webp',
    ];

    public static function getGroups(): array
    {
        return ['test'];
    }

    public function load(ObjectManager $manager): void
    {
        // Gestion de l'index (path) pour ne pas le voir reset au passage à la boucle des invités
        $fileIndex = 1;

        // Création des media d'Ina Zaoui
        foreach (range(1, 5) as $albumIndex) {
            $album = $this->getReference(sprintf('album%d', $albumIndex), Album::class);
            foreach (range(1, 10) as $i) {
                $media = new Media();
                $media->setUser($this->getReference('user_ina', User::class));
                $media->setAlbum($album);
                $media->setPath(self::TEST_IMAGES[$fileIndex % 5]);
                $media->setTitle(sprintf('Titre %d', $fileIndex));
                $manager->persist($media);

                ++$fileIndex;
            }
        }

        // Création des media des invités (avec chacun 50 médias)
        foreach (range(1, 100) as $guestIndex) {
            $guest = $this->getReference(sprintf('user_guest%d', $guestIndex), User::class);
            foreach (range(1, 50) as $titleIndex) {
                $media = new Media();
                $media->setUser($guest);
                $media->setAlbum(null);
                $media->setPath(self::TEST_IMAGES[$fileIndex % 5]);
                $media->setTitle(sprintf('Titre %d', $titleIndex));
                $manager->persist($media);

                ++$fileIndex;
            }
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
            AlbumTestFixtures::class,
        ];
    }
}
