<?php

namespace App\DataAccessLayer\Repository;

use App\Domain\Entity\QuizAnswer;
use App\Domain\Entity\QuizQuestion;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<QuizAnswer>
 */
class QuizAnswerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, QuizAnswer::class);
    }

    public function getNextOrder(QuizQuestion $quizQuestion): int
    {
        $order = 0;
        $result = $this->createQueryBuilder('qa')
            ->select('MAX(qa.order) AS maxOrder')
            ->where('qa.question = :question')
            ->setParameter('question', $quizQuestion->getUuid()->toBinary())
            ->getQuery()
            ->getSingleScalarResult();

        if (is_numeric($result)) {
            $order = (int)$result;
        }

        return $order + 1;
    }
}