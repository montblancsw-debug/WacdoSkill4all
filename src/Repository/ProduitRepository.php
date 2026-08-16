<?php

namespace App\Repository;

use App\Entity\Categorie;
use App\Entity\Produit;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

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

    // Recherche un produit dans une catégorie précise.
    public function searchByCategorie(
        Categorie $categorie,
        string $value
    ): array {
        return $this
            ->createQueryBuilder('p')
            ->andWhere('p.categorie = :categorie')
            ->andWhere('p.produit_nom LIKE :val')
            ->setParameter(
                'categorie',
                $categorie
            )
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