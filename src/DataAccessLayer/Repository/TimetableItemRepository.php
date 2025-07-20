<?php

namespace App\DataAccessLayer\Repository;

use App\Domain\Entity\TimetableDay;
use App\Domain\Entity\TimetableItem;
use App\Domain\Enum\TimetableLocationType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TimetableItem>
 */
class TimetableItemRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TimetableItem::class);
    }

    public function getByDayAndLocationType(
        TimetableDay          $timetableDay,
        TimetableLocationType $timetableLocationType
    ): array {
        return $this->createQueryBuilder('ti')
            ->leftJoin('ti.timetableLocation', 'tl')
            ->leftJoin('ti.volunteer', 'v')
            ->where('ti.timetableDay = :day')
            ->andWhere('tl.timetableLocationType = :locationType')
            ->setParameter('day', $timetableDay->getUuid()->toBinary())
            ->setParameter('locationType', $timetableLocationType->value)
            ->orderBy('ti.startTime', 'ASC')
            ->getQuery()
            ->getResult();
    }
}