<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Vich\UploaderBundle\Mapping\PropertyMapping;
use Vich\UploaderBundle\Naming\NamerInterface;

final class OriginalFileNamer implements NamerInterface
{

// ====================================================== //
// ====================== METHODES ======================= //
// ====================================================== //

    // Conserve le nom d'origine du fichier envoyé.
    public function name(object $object, PropertyMapping $mapping): string
    {
        // Récupère le fichier envoyé par l'utilisateur.
        $file = $mapping->getFile($object);

        // Vérifie que le fichier envoyé est valide.
        if (!$file instanceof UploadedFile)
        {
            throw new \LogicException('Aucun fichier valide n\'a été envoyé.');
        }

    // Retourne uniquement le nom du fichier sans son chemin.
        return basename($file->getClientOriginalName());
    }
}