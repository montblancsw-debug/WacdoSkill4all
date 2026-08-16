<?php

namespace App\Repository;

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

    // Recherche les produits par leur nom.
    public function search(
        string $value
    ): array {
        return $this
            ->createQueryBuilder('p')
            ->andWhere('p.produit_nom LIKE :val')
            ->setParameter(
                'val',
                '%' . $value . '%'
            )
            ->orderBy(
                'p.produit_nom',
                'ASC'
            )
            ->getQuery()
            ->getResult();
    }
}