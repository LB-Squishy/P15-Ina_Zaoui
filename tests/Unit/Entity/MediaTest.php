<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Album;
use App\Entity\Media;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

class MediaTest extends TestCase
{
    public function testUser(): void
    {
        $user = new User();
        $user->setName('testname');
        $media = new Media();
        $media->setUser($user);

        $this->assertSame('testname', $media->getUser()->getName(), 'Le nom de l\'utilisateur ne correspond pas à la valeur attendue');
    }

    public function testPath(): void
    {
        $media = new Media();
        $media->setPath('/uploads/test.webp');

        $this->assertSame('/uploads/test.webp', $media->getPath(), 'Le nom de chemin du media ne correspond pas à la valeur attendue');
    }

    public function testTitle(): void
    {
        $media = new Media();
        $media->setTitle('testTitle');

        $this->assertSame('testTitle', $media->getTitle(), 'Le titre ne correspond pas à la valeur attendue');
    }

    public function testAlbum(): void
    {
        $album = new Album();
        $album->setName('testAlbum');
        $media = new Media();
        $media->setAlbum($album);

        $this->assertSame('testAlbum', $media->getAlbum()->getName(), 'Le nom de l\'album ne correspond pas à la valeur attendue');
    }
}
