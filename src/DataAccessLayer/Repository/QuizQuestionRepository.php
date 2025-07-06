<?php

namespace App\DataAccessLayer\Repository;

use App\Domain\Entity\QuizQuestion;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use RuntimeException;

/**
 * @extends ServiceEntityRepository<QuizQuestion>
 */
class QuizQuestionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, QuizQuestion::class);
    }

    public function getOrdered(): array
    {
        return $this->createQueryBuilder('cqq')
            ->orderBy('cqq.order', 'ASC')
            ->leftJoin('cqq.answers', 'cqa')
            ->getQuery()
            ->getResult();
    }

    public function getNextOrder(): int
    {
        $result = $this->createQueryBuilder('cqq')
            ->select('MAX(cqq.order) AS maxOrder')
            ->getQuery()
            ->getSingleScalarResult();

        if (!is_int($result)) {
            throw new RuntimeException("ORDER value is not an integer.");
        }

        return $result + 1;
    }
}