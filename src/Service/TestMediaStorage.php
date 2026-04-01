<?php

namespace App\Service;

class TestMediaStorage extends MediaStorage
{
    /**
     * Ne supprime pas réellement les fichiers en test (surcharge de la méthode delete de MediaStorage)
     * @param string $path le chemin du fichier à supprimer
     * @return void
     */
    public function delete(string $path): void
    {
        // Ne fait rien pour ne pas supprimer les fichiers du dossier en test
    }
}
