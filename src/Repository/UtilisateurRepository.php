<?php

namespace App\Repository;

use App\Entity\Utilisateur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Utilisateur>
 */
class UtilisateurRepository extends ServiceEntityRepository
{
    public function __construct(
        ManagerRegistry $registry
    ) {
        parent::__construct(
            $registry,
            Utilisateur::class
        );
    }

    // Recherche et filtre les utilisateurs.
    public function search(
        string $value = '',
        string $role = ''
    ): array {
        $value = trim(
            $value
        );

        $role = trim(
            $role
        );

        $queryBuilder = $this
            ->createQueryBuilder('u');

        if ($value !== '') {
            $queryBuilder
                ->andWhere(
                    'u.utilisateur_nom LIKE :val
                    OR u.utilisateur_prenom LIKE :val
                    OR u.utilisateur_email LIKE :val
                    OR u.utilisateur_role LIKE :val'
                )
                ->setParameter(
                    'val',
                    '%' . $value . '%'
                );

            // Accepte par exemple 16 ou #16.
            $idValue = ltrim(
                $value,
                '#'
            );

            if (ctype_digit($idValue)) {
                $queryBuilder
                    ->orWhere(
                        'u.id = :id'
                    )
                    ->setParameter(
                        'id',
                        (int) $idValue
                    );
            }
        }

        if ($role !== '') {
            $queryBuilder
                ->andWhere(
                    'u.utilisateur_role = :role'
                )
                ->setParameter(
                    'role',
                    $role
                );
        }

        return $queryBuilder
            ->orderBy(
                'u.utilisateur_nom',
                'ASC'
            )
            ->addOrderBy(
                'u.utilisateur_prenom',
                'ASC'
            )
            ->getQuery()
            ->getResult();
    }
}