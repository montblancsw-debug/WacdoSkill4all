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

    // Recherche une catégorie par son nom ou son ID.
    public function search(
        string $value
    ): array {
        $value = trim(
            $value
        );

        $queryBuilder = $this
            ->createQueryBuilder('c')
            ->andWhere(
                'c.categorie_nom LIKE :val'
            )
            ->setParameter(
                'val',
                '%' . $value . '%'
            );

        // Accepte par exemple 20 ou #20.
        $idValue = ltrim(
            $value,
            '#'
        );

        if (ctype_digit($idValue)) {
            $queryBuilder
                ->orWhere(
                    'c.id = :id'
                )
                ->setParameter(
                    'id',
                    (int) $idValue
                );
        }

        return $queryBuilder
            ->orderBy(
                'c.categorie_nom',
                'ASC'
            )
            ->getQuery()
            ->getResult();
    }
}