<?php

namespace App\Repository;

use App\Entity\Categorie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
class CategorieRepository extends ServiceEntityRepository
{

// ====================================================== //
// ==================== CONSTRUCTEUR. =================== //
// ====================================================== //

// Initialise le repository de l'entité Categorie.

    public function __construct(ManagerRegistry $registry) {parent::__construct($registry,Categorie::class);
    }

// ====================================================== //
// ====================== METHODES. ===================== //
// ====================================================== //

    // Recherche une catégorie par son nom ou son identifiant.
    public function search(string $value): array {$value = trim($value);

    // Prépare la recherche sur le nom de la catégorie.
        $queryBuilder = $this->createQueryBuilder('c')->andWhere('c.categorie_nom LIKE :val')->setParameter('val','%' . $value . '%');
        $idValue = ltrim($value,'#');

    // Ajoute la recherche par identifiant si la valeur est numérique.
        if (ctype_digit($idValue))
        {
            $queryBuilder->orWhere('c.id = :id')->setParameter('id',(int) $idValue);
        }
        return $queryBuilder->orderBy('c.categorie_nom','ASC')->getQuery()->getResult();
    }
}