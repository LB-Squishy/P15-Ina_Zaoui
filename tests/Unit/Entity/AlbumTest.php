<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Album;
use PHPUnit\Framework\TestCase;

class AlbumTest extends TestCase
{
    public function testName(): void
    {
        $album = new Album();
        $album->setName('testAlbumName');

        $this->assertSame('testAlbumName', $album->getName(), 'Le nom de l\'album ne correspond pas à la valeur attendue');
    }
}
