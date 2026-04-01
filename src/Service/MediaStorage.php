<?php

namespace App\Service;

class MediaStorage
{
    /**
     * Supprime un fichier du système de fichiers
     * @param string $path le chemin du fichier à supprimer
     * @return void
     */
    public function delete(string $path): void
    {
        if (is_file($path)) {
            unlink($path);
        }
    }
}
