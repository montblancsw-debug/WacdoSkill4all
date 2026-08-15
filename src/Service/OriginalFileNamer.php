<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Vich\UploaderBundle\Mapping\PropertyMapping;
use Vich\UploaderBundle\Naming\NamerInterface;

// Permet de Mapper les noms des fichiers .png en gardant leurs noms Originel
final class OriginalFileNamer implements NamerInterface
{
    public function name(object $object, PropertyMapping $mapping): string
    {
        $file = $mapping->getFile($object);

        if (!$file instanceof UploadedFile) {
            throw new \LogicException(
                'Aucun fichier valide n\'a été envoyé.'
            );
        }

        return basename(
            $file->getClientOriginalName()
        );
    }
}