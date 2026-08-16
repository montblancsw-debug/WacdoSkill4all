<?php

namespace App\Repository;

use App\Entity\Categorie;
use App\Entity\Produit;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Produit>
 */
class ProduitRepository extends ServiceEntityRepository
{
    public function __construct(
        ManagerRegistry $registry
    ) {
        parent::__construct(
            $registry,
            Produit::class
        );
    }

    // Recherche un produit par son nom ou son ID.
    public function search(
        string $value
    ): array {
        $value = trim(
            $value
        );

        $queryBuilder = $this
            ->createQueryBuilder('p')
            ->andWhere(
                'p.produit_nom LIKE :val'
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
                    'p.id = :id'
                )
                ->setParameter(
                    'id',
                    (int) $idValue
                );
        }

        return $queryBuilder
            ->orderBy(
                'p.produit_nom',
                'ASC'
            )
            ->getQuery()
            ->getResult();
    }

    // Recherche un produit dans une catégorie précise.
    public function searchByCategorie(
        Categorie $categorie,
        string $value
    ): array {
        return $this
            ->createQueryBuilder('p')
            ->andWhere(
                'p.categorie = :categorie'
            )
            ->andWhere(
                'p.produit_nom LIKE :val'
            )
            ->setParameter(
                'categorie',
                $categorie
            )
            ->setParameter(
                'val',
                '%' . trim($value) . '%'
            )
            ->orderBy(
                'p.produit_nom',
                'ASC'
            )
            ->getQuery()
            ->getResult();
    }
}