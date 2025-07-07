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
            ->getQuery()
            ->getResult();
    }

    public function getNextOrder(): int
    {
        $order = 0;
        $result = $this->createQueryBuilder('qq')
            ->select('MAX(qq.order) AS maxOrder')
            ->getQuery()
            ->getSingleScalarResult();

        if (is_numeric($result)) {
            $order = (int) $result;
        }

        return $order + 1;
    }
}