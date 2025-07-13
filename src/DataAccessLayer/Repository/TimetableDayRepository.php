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

    /**
     * @return TimetableDay[]
     */
    public function getOrdered(): array
    {
        return $this->createQueryBuilder('td')
            ->orderBy('td.order', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function getNextOrder(): int
    {
        $order = 0;
        $result = $this->createQueryBuilder('td')
            ->select('MAX(td.order) AS maxOrder')
            ->getQuery()
            ->getSingleScalarResult();

        if (is_numeric($result)) {
            $order = (int)$result;
        }

        return $order + 1;
    }
}