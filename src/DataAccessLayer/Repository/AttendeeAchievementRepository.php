<?php

namespace App\DataAccessLayer\Repository;

use App\Domain\Entity\AttendeeAchievement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<AttendeeAchievement>
 * @template-extends ServiceEntityRepository<AttendeeAchievement>
 */
class AttendeeAchievementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AttendeeAchievement::class);
    }
}