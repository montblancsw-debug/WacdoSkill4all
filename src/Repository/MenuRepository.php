<?php

namespace App\Repository;

use App\Entity\Menu;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
class MenuRepository extends ServiceEntityRepository
{

// ====================================================== //
// ==================== CONSTRUCTEUR. =================== //
// ====================================================== //

    // Initialise le repository de l'entité Menu.
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Menu::class);
    }

// ====================================================== //
// ====================== METHODES. ===================== //
// ====================================================== //

    // Recherche et filtre les menus.
    public function search(string $value = '', string $status = ''): array
    {
        // Prépare la requête avec les produits associés aux menus.
        $queryBuilder = $this->createQueryBuilder('m')->leftJoin('m.produits', 'p')->addSelect('p')->distinct();

        // Recherche par nom de menu, produit ou identifiant.
        $value = trim($value);
        if ($value !== '')
        {
            $conditions = $queryBuilder->expr()->orX($queryBuilder->expr()->like('m.menu_nom', ':value'),$queryBuilder->expr()->like('p.produit_nom', ':value'));
            $idValue = ltrim($value, '#');
            if (ctype_digit($idValue))
            {
                $conditions->add($queryBuilder->expr()->eq('m.id', ':id'));
                $queryBuilder->setParameter('id', (int) $idValue);
            }
            $queryBuilder->andWhere($conditions)->setParameter('value', '%' . $value . '%');
        }
        // Filtre les menus selon leur statut.
        if ($status === '1' || $status === '0')
        {
            $queryBuilder->andWhere('m.menu_status = :status')->setParameter('status', $status === '1');
        }
        // Trie les menus par nom dans l'ordre alphabétique.
        return $queryBuilder->orderBy('m.menu_nom', 'ASC')->getQuery()->getResult();
    }

    // Retourne tous les menus actifs avec leurs produits.
    public function findActive(): array
    {
        return $this
            ->createQueryBuilder('m')
            ->leftJoin('m.produits', 'p')
            ->addSelect('p')
            ->andWhere('m.menu_status = :status')
            ->setParameter('status', true)
            ->orderBy('m.menu_nom', 'ASC')
            ->getQuery()
            ->getResult();
    }

    // Retourne un menu actif selon son identifiant.
    public function findOneActive(int $id): ?Menu
    {
        return $this
            ->createQueryBuilder('m')
            ->leftJoin('m.produits', 'p')
            ->addSelect('p')
            ->andWhere('m.id = :id')
            ->andWhere('m.menu_status = :status')
            ->setParameter('id', $id)
            ->setParameter('status', true)
            ->getQuery()
            ->getOneOrNullResult();
    }
}