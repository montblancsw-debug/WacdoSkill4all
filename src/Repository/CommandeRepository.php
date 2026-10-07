<?php

namespace App\Repository;

use App\Entity\Commande;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class CommandeRepository extends ServiceEntityRepository
{

    // ====================================================== //
    // ==================== CONSTRUCTEUR ==================== //
    // ====================================================== //

    // Initialise le repository de l'entité Commande.
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry,Commande::class);
    }

    // ====================================================== //
    // ====================== METHODES ====================== //
    // ====================================================== //

    // Recherche une commande par son identifiant, son statut ou son utilisateur.
    public function search(string $value): array
    {
        $value = trim($value);
        $queryBuilder = $this->createQueryBuilder('c')->leftJoin('c.utilisateur','u');
        $conditionsRecherche = $queryBuilder->expr()->orX
        (
            'c.commande_statut LIKE :val','u.utilisateur_nom LIKE :val','u.utilisateur_prenom LIKE :val','u.utilisateur_email LIKE :val'
        );

        $queryBuilder->andWhere($conditionsRecherche)->setParameter('val','%' . $value . '%');

        // Permet également de rechercher une commande par son identifiant.
        $idValue = ltrim($value,'#');

        if (ctype_digit($idValue))
        {
            $queryBuilder->orWhere('c.id = :id')->setParameter('id',(int) $idValue);
        }

        return $queryBuilder->orderBy('c.commande_date','DESC')
            ->addOrderBy('c.id','DESC')
            ->getQuery()
            ->getResult();
    }

    // retourne uniquement les commandes encore à préparer.
    public function findForPreparation(string $value = ''): array
    {
        $value = trim($value);
        $queryBuilder = $this->createQueryBuilder('c')
            ->leftJoin('c.utilisateur','u')->andWhere('c.commande_statut IN (:statuts)')
            ->setParameter('statuts',['en attente','en préparation',]);

        // applique la recherche tout en conservant uniquement les commandes à préparer.
        if ($value !== '')
        {
            $conditionsRecherche = $queryBuilder->expr()->orX
            (
                'c.commande_statut LIKE :val',
                'u.utilisateur_nom LIKE :val',
                'u.utilisateur_prenom LIKE :val',
                'u.utilisateur_email LIKE :val'
            );
            $idValue = ltrim($value,'#');
            if (ctype_digit($idValue))
            {
                $conditionsRecherche->add('c.id = :id');
                $queryBuilder->setParameter('id',(int) $idValue);
            }
            $queryBuilder->andWhere($conditionsRecherche)->setParameter('val','%' . $value . '%');
        }
        // tri, par heure de livraison croissante.
        return $queryBuilder->orderBy('c.commande_horaire_livraison','ASC')->addOrderBy('c.id','ASC')->getQuery()->getResult();
    }
}