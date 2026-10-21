<?php

namespace App\DataAccessLayer\Repository;

use App\Domain\Entity\LiveQuizQuestion;
use App\Domain\Entity\LiveQuizSession;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<LiveQuizQuestion>
 */
class LiveQuizQuestionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LiveQuizQuestion::class);
    }

    public function findLatestPushed(LiveQuizSession $session): ?LiveQuizQuestion
    {
        return $this->createQueryBuilder('q')
            ->where('q.session = :session')
            ->andWhere('q.openedAt IS NOT NULL')
            ->setParameter('session', $session->getUuid()->toBinary())
            ->orderBy('q.openedAt', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
