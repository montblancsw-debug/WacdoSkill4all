<?php

namespace App\Repository;

use App\Entity\Categorie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Categorie>
 */
class CategorieRepository extends ServiceEntityRepository
{
    public function __construct(
        ManagerRegistry $registry
    ) {
        parent::__construct(
            $registry,
            Categorie::class
        );
    }

    // Recherche les catégories par leur nom.
    public function search(
        string $value
    ): array {
        return $this
            ->createQueryBuilder('c')
            ->andWhere('c.categorie_nom LIKE :val')
            ->setParameter(
                'val',
                '%' . $value . '%'
            )
            ->orderBy(
                'c.categorie_nom',
                'ASC'
            )
            ->getQuery()
            ->getResult();
    }
}