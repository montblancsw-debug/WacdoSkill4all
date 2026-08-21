<?php

namespace App\Repository;

use App\Entity\Detail;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
class DetailRepository extends ServiceEntityRepository
{
// ====================================================== //
// ==================== CONSTRUCTEUR. =================== //
// ====================================================== //

// Initialise le repository de l'entité Detail.

    public function __construct(ManagerRegistry $registry){
        parent::__construct($registry, Detail::class);
    }
}
