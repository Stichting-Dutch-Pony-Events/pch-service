<?php

namespace App\DataAccessLayer\Repository;

use App\Domain\Entity\LiveQuizOption;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<LiveQuizOption>
 */
class LiveQuizOptionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LiveQuizOption::class);
    }
}
