<?php

namespace App\Repository;

use App\Entity\Categorie;
use App\Entity\Produit;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ProduitRepository extends ServiceEntityRepository
{

// ====================================================== //
// ==================== CONSTRUCTEUR. =================== //
// ====================================================== //

// Initialise le repository de l'entité Produit.

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry,Produit::class);
    }

// ====================================================== //
// ====================== METHODES. ===================== //
// ====================================================== //

// Recherche, filtre et trie les produits.

    public function search(string $value = '',string $stock = '',string $categorieId = ''): array
    {
        $value = trim($value);
        $stock = trim($stock);
        $categorieId = trim($categorieId);

        // Prépare la requête avec la catégorie associée au produit.
        $queryBuilder = $this->createQueryBuilder('p')->leftJoin('p.categorie','c')->addSelect('c');

        // Recherche par identifiant, nom, description ou catégorie.
        if ($value !== '') {$conditions = $queryBuilder->expr()->orX();
            $conditions->add($queryBuilder->expr()->like('p.produit_nom',':val'));
            $conditions->add($queryBuilder->expr()->like('p.produit_description',':val'));
            $conditions->add($queryBuilder->expr()->like('c.categorie_nom',':val'));
            $queryBuilder->setParameter('val','%' . $value . '%');
            $idValue = ltrim($value,'#');

            if (ctype_digit($idValue))
            {
                $conditions->add($queryBuilder->expr()->eq('p.id',':id'));
                $queryBuilder->setParameter('id',(int) $idValue);
            }
            $queryBuilder->andWhere($conditions);
        }

        // Filtre les produits selon la catégorie sélectionnée.
        if ($categorieId !== ''&& ctype_digit($categorieId))
        {
            $queryBuilder->andWhere('c.id = :categorieId')->setParameter('categorieId',(int) $categorieId);
        }

        // Filtre les produits selon leur disponibilité.
        if ($stock === '1'|| $stock === '0')
        {
            $queryBuilder->andWhere('p.produit_stock = :stock')->setParameter('stock',$stock === '1');
        }

        // Trie les produits par nom dans l'ordre alphabétique.
        return $queryBuilder->orderBy('p.produit_nom','ASC')->getQuery()->getResult();
    }

    // Recherche un produit par son nom dans une catégorie précise.
    public function searchByCategorie(Categorie $categorie,string $value): array
    {
        return $this
            ->createQueryBuilder('p')
            ->andWhere('p.categorie = :categorie')
            ->andWhere('p.produit_nom LIKE :val')
            ->setParameter('categorie',$categorie)
            ->setParameter('val','%' . trim($value) . '%')
            ->orderBy('p.produit_nom','ASC')
            ->getQuery()
            ->getResult();
    }
}