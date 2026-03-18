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

        $manager->flush();
    }
}
