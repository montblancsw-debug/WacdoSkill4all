<?php

namespace App\Repository;

use App\Entity\Commande;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class CommandeRepository extends ServiceEntityRepository
{
// ====================================================== //
// ==================== CONSTRUCTEUR. =================== //
// ====================================================== //

// Initialise le repository de l'entité Commande.

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Commande::class);
    }

// ====================================================== //
// ====================== METHODES. ===================== //
// ====================================================== //

// Recherche une commande par son identifiant, son statut ou son utilisateur.

    public function search(string $value): array
    {
        $value = trim($value);

// Prépare la recherche avec l'utilisateur associé à la commande.

        $queryBuilder = $this
            ->createQueryBuilder('c')
            ->leftJoin('c.utilisateur', 'u')
            ->andWhere(
                'c.commande_statut LIKE :val
                OR u.utilisateur_nom LIKE :val
                OR u.utilisateur_prenom LIKE :val
                OR u.utilisateur_email LIKE :val'
            )
            ->setParameter('val', '%' . $value . '%');

// Permet également de rechercher une commande par son identifiant.

        $idValue = ltrim($value, '#');

        if (ctype_digit($idValue)) {
            $queryBuilder
                ->orWhere('c.id = :id')
                ->setParameter('id', (int) $idValue);
        }

// Trie les commandes par identifiant décroissant.

        return $queryBuilder
            ->orderBy('c.id', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
