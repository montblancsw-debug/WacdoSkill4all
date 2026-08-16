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

    // Recherche et filtre les produits.
    public function search(
        string $value = '',
        string $stock = '',
        string $categorieId = ''
    ): array {
        $value = trim($value);
        $stock = trim($stock);
        $categorieId = trim($categorieId);

        $queryBuilder = $this
            ->createQueryBuilder('p')
            ->leftJoin(
                'p.categorie',
                'c'
            );

        // Recherche par nom ou ID.
        if ($value !== '') {
            $conditions = $queryBuilder
                ->expr()
                ->orX();

            $conditions->add(
                $queryBuilder
                    ->expr()
                    ->like(
                        'p.produit_nom',
                        ':val'
                    )
            );

            $queryBuilder->setParameter(
                'val',
                '%' . $value . '%'
            );

            $idValue = ltrim(
                $value,
                '#'
            );

            if (ctype_digit($idValue)) {
                $conditions->add(
                    $queryBuilder
                        ->expr()
                        ->eq(
                            'p.id',
                            ':id'
                        )
                );

                $queryBuilder->setParameter(
                    'id',
                    (int) $idValue
                );
            }

            $queryBuilder->andWhere(
                $conditions
            );
        }

        // Filtre selon la catégorie.
        if (
            $categorieId !== ''
            && ctype_digit($categorieId)
        ) {
            $queryBuilder
                ->andWhere(
                    'c.id = :categorieId'
                )
                ->setParameter(
                    'categorieId',
                    (int) $categorieId
                );
        }

        // Filtre selon la disponibilité.
        if (
            $stock === '1'
            || $stock === '0'
        ) {
            $queryBuilder
                ->andWhere(
                    'p.produit_stock = :stock'
                )
                ->setParameter(
                    'stock',
                    $stock === '1'
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