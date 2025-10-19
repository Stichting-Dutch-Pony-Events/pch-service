<?php

namespace App\DataAccessLayer\Repository;

use App\Domain\Entity\AttendeeAchievement;
use DateTime;
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

    public function getTimeFirstAchievement(): DateTime
    {
        $res = $this->createQueryBuilder('aa')
            ->select('MIN(aa.achievedAt)')
            ->getQuery()
            ->getSingleScalarResult();

        return new DateTime($res);
    }
}