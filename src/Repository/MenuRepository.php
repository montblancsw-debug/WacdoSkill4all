<?php

namespace App\Repository;

use App\Entity\Menu;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/** @extends ServiceEntityRepository<Menu> */
class MenuRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Menu::class);
    }

    public function search(string $value = '', string $status = ''): array
    {
        $queryBuilder = $this
            ->createQueryBuilder('m')
            ->leftJoin('m.produits', 'p')
            ->addSelect('p')
            ->distinct();

        $value = trim($value);

        if ($value !== '') {
            $conditions = $queryBuilder->expr()->orX(
                $queryBuilder->expr()->like('m.menu_nom', ':value'),
                $queryBuilder->expr()->like('p.produit_nom', ':value')
            );

            $idValue = ltrim($value, '#');

            if (ctype_digit($idValue)) {
                $conditions->add($queryBuilder->expr()->eq('m.id', ':id'));
                $queryBuilder->setParameter('id', (int) $idValue);
            }

            $queryBuilder
                ->andWhere($conditions)
                ->setParameter('value', '%' . $value . '%');
        }

        if ($status === '1' || $status === '0') {
            $queryBuilder
                ->andWhere('m.menu_status = :status')
                ->setParameter('status', $status === '1');
        }

        return $queryBuilder
            ->orderBy('m.menu_nom', 'ASC')
            ->getQuery()
            ->getResult();
    }

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