<?php

namespace App\DataFixtures;

use App\Entity\Album;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AlbumFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // Création de 5 albums
        foreach (range(1, 5) as $i) {
            $album = new Album();
            $album->setName(sprintf('Album %d', $i));
            $manager->persist($album);

            $this->addReference(sprintf('album%d', $i), $album);
        }
        // Création d'un album supplémentaire pour les tests d'album sans médias
        $album = new Album();
        $album->setName('Album 6');
        $manager->persist($album);
        $this->addReference('album6', $album);

        $manager->flush();
    }
}
