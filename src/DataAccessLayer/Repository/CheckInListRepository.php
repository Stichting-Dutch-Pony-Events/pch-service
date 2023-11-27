<?php

namespace App\DataAccessLayer\Repository;

use App\Domain\Entity\CheckInList;
use App\Domain\Enum\CheckInListType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @template-extends ServiceEntityRepository<CheckInList>
 */
class CheckInListRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CheckInList::class);
    }

    public function findActiveCheckInList(CheckInListType $type): ?CheckInList
    {
        return $this->createQueryBuilder('cil')
            ->where('cil.type = :type')
            ->andWhere('cil.startTime <= :now')
            ->andWhere('cil.endTime >= :now')
            ->orderBy('cil.startTime', 'ASC')
            ->setMaxResults(1)
            ->setParameter('type', $type->value)
            ->setParameter('now', new \DateTime())
            ->getQuery()
            ->execute()[0] ?? null;
    }
}
