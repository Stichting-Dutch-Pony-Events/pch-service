<?php

namespace App\DataAccessLayer\Repository;

use App\Domain\Entity\Announcement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Announcement>
 * @template-extends ServiceEntityRepository<Announcement>
 */
class AnnouncementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Announcement::class);
    }

    /**
     * @return Announcement[]
     */
    public function lastTen(): array
    {
        return $this->createQueryBuilder('a')
            ->orderBy('a.createdAt', 'DESC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();
    }
}
