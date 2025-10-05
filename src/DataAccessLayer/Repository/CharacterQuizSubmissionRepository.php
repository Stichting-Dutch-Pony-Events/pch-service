<?php

namespace App\DataAccessLayer\Repository;

use App\Domain\Entity\Attendee;
use App\Domain\Entity\CharacterQuizSubmission;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CharacterQuizSubmission>
 */
class CharacterQuizSubmissionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CharacterQuizSubmission::class);
    }

    public function lastForAttendee(Attendee $attendee): ?CharacterQuizSubmission
    {
        return $this->createQueryBuilder('sc')
            ->select('sc', 'tr', 't')
            ->where('sc.attendee = :attendee')
            ->setParameter('attendee', $attendee->getUuid()->toBinary())
            ->orderBy('sc.createdAt', 'DESC')
            ->leftJoin('sc.teamResults', 'tr')
            ->leftJoin('tr.team', 't')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}