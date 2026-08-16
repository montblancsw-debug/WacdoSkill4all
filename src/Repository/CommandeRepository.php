<?php

namespace App\Repository;

use App\Entity\Commande;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Commande>
 */
class CommandeRepository extends ServiceEntityRepository
{
    public function __construct(
        ManagerRegistry $registry
    ) {
        parent::__construct(
            $registry,
            Commande::class
        );
    }

    // Recherche une commande.
    public function search(
        string $value
    ): array {
        $queryBuilder = $this
            ->createQueryBuilder('c')
            ->leftJoin(
                'c.utilisateur',
                'u'
            )
            ->andWhere(
                'c.commande_statut LIKE :val
                OR u.utilisateur_nom LIKE :val
                OR u.utilisateur_prenom LIKE :val
                OR u.utilisateur_email LIKE :val'
            )
            ->setParameter(
                'val',
                '%' . $value . '%'
            );

        // Permet également la recherche par numéro.
        if (ctype_digit($value)) {
            $queryBuilder
                ->orWhere('c.id = :id')
                ->setParameter(
                    'id',
                    (int) $value
                );
        }

        return $queryBuilder
            ->orderBy(
                'c.id',
                'DESC'
            )
            ->getQuery()
            ->getResult();
    }
}