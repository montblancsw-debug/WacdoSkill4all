<?php

namespace App\Repository;

use App\Entity\Utilisateur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
class UtilisateurRepository extends ServiceEntityRepository
{

// ====================================================== //
// ==================== CONSTRUCTEUR. =================== //
// ====================================================== //

    // Initialise le repository de l'entité Utilisateur.
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry,Utilisateur::class);
    }

// ====================================================== //
// ====================== METHODES. ===================== //
// ====================================================== //

    // Recherche, filtre et trie les utilisateurs.
    public function search(string $value = '',string $role = '',string $triRole = ''): array
    {
        $value = trim($value);$role = trim($role);$triRole = trim($triRole);

        // Prépare la requête de recherche des utilisateurs.
        $queryBuilder = $this->createQueryBuilder('u');

        // Recherche par nom, prénom, email ou identifiant.
        if ($value !== '')
        {
            $conditions = $queryBuilder->expr()->orX();
            $conditions->add('u.utilisateur_nom LIKE :val');
            $conditions->add('u.utilisateur_prenom LIKE :val');
            $conditions->add('u.utilisateur_email LIKE :val');
            $queryBuilder->setParameter('val','%' . $value . '%');
            $idValue = ltrim($value,'#');

            if (ctype_digit($idValue))
            {
                $conditions->add('u.id = :id');
                $queryBuilder->setParameter('id',(int) $idValue);
            }

            $queryBuilder->andWhere($conditions);
        }

        // Filtre selon le rôle.
        if ($role !== '')
        {
            $queryBuilder->andWhere('u.utilisateur_role = :role')->setParameter('role',$role);
        }

        // Trie les résultats selon le rôle.
        if ($triRole === 'asc')
        {
            $queryBuilder->orderBy('u.utilisateur_role','ASC')->addOrderBy('u.utilisateur_nom','ASC');
        } elseif (
            $triRole === 'desc')
        {
            $queryBuilder->orderBy('u.utilisateur_role','DESC')->addOrderBy('u.utilisateur_nom','ASC');

        // Utilise le nom et le prénom comme tri par défaut.
        } else {
            $queryBuilder->orderBy('u.utilisateur_nom','ASC')->addOrderBy('u.utilisateur_prenom','ASC');
        }
        return $queryBuilder->getQuery()->getResult();
    }
}