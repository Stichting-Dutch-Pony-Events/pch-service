<?php

namespace App\DataAccessLayer\Repository;

use App\Domain\Entity\TimetableLocation;
use App\Domain\Enum\TimetableLocationType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TimetableLocation>
 */
class TimetableLocationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TimetableLocation::class);
    }

    /**
     * @param TimetableLocationType $locationType
     * @return TimetableLocation[]
     */
    public function getByType(TimetableLocationType $locationType): array
    {
        return $this->createQueryBuilder('t')
            ->where('t.timetableLocationType = :type')
            ->setParameter('type', $locationType->value)
            ->orderBy('t.order', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function getNextOrder(TimetableLocationType $locationType): int
    {
        $order = 0;
        $result = $this->createQueryBuilder('tl')
            ->select('MAX(tl.order) AS maxOrder')
            ->where('tl.timetableLocationType = :type')
            ->setParameter('type', $locationType->value)
            ->getQuery()
            ->getSingleScalarResult();

        if (is_numeric($result)) {
            $order = (int)$result;
        }

        return $order + 1;
    }

    /**
     * @return TimetableLocation[]
     */
    public function getPublicTimetableLocations(): array
    {
        return $this->createQueryBuilder('tl')
            ->select('tl, ti')
            ->leftJoin('tl.timetableItems', 'ti')
            ->where('tl.timetableLocationType = :type')
            ->setParameter('type', TimetableLocationType::ROOM->value)
            ->orderBy('tl.order', 'ASC')
            ->getQuery()
            ->getResult();
    }
}