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

    // Recherche un utilisateur.
    public function search(
        string $value
    ): array {
        return $this
            ->createQueryBuilder('u')
            ->andWhere(
                'u.utilisateur_nom LIKE :val
                OR u.utilisateur_prenom LIKE :val
                OR u.utilisateur_email LIKE :val
                OR u.utilisateur_role LIKE :val'
            )
            ->setParameter(
                'val',
                '%' . $value . '%'
            )
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