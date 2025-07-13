<?php

namespace App\DataAccessLayer\Repository;

use App\Domain\Entity\TimetableDay;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TimetableDay>
 */
class TimetableDayRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TimetableDay::class);
    }
}