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
        $value = trim(
            $value
        );

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

        // Accepte par exemple 9 ou #9.
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
                'c.id',
                'DESC'
            )
            ->getQuery()
            ->getResult();
    }
}