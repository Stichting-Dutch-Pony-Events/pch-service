<?php

namespace App\DataAccessLayer\Repository;

use App\Domain\Entity\LiveQuizOption;
use App\Domain\Entity\LiveQuizQuestion;
use App\Domain\Entity\LiveQuizResponse;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Uid\Uuid;

/**
 * @extends ServiceEntityRepository<LiveQuizResponse>
 */
class LiveQuizResponseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LiveQuizResponse::class);
    }

    /** @return list<array{option: LiveQuizOption, count: int}> */
    public function countByQuestionGroupedByOption(LiveQuizQuestion $question): array
    {
        $counts = $this->createQueryBuilder('r')
            ->select('IDENTITY(r.option) AS optionId, COUNT(r.id) AS cnt')
            ->where('r.question = :question')
            ->setParameter('question', $question->getUuid()->toBinary())
            ->groupBy('r.option')
            ->getQuery()
            ->getResult();

        $countMap = [];
        foreach ($counts as $row) {
            // IDENTITY() yields the raw binary column, while getId() is the RFC 4122 string.
            $countMap[Uuid::fromString($row['optionId'])->toRfc4122()] = (int) $row['cnt'];
        }

        $result = [];
        foreach ($question->getOptions() as $option) {
            $result[] = [
                'option' => $option,
                'count'  => $countMap[$option->getId()] ?? 0,
            ];
        }

        return $result;
    }
}
