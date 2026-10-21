<?php

namespace App\DataAccessLayer\Repository;

use App\Domain\Entity\Attendee;
use App\Domain\Entity\LiveQuizParticipant;
use App\Domain\Entity\LiveQuizSession;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<LiveQuizParticipant>
 */
class LiveQuizParticipantRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LiveQuizParticipant::class);
    }

    public function findBySessionAndAttendee(LiveQuizSession $session, Attendee $attendee): ?LiveQuizParticipant
    {
        return $this->createQueryBuilder('p')
            ->where('p.session = :session')
            ->andWhere('p.attendee = :attendee')
            ->setParameter('session', $session->getUuid()->toBinary())
            ->setParameter('attendee', $attendee->getUuid()->toBinary())
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findLatestActiveForAttendee(Attendee $attendee): ?LiveQuizParticipant
    {
        return $this->createQueryBuilder('p')
            ->join('p.session', 's')
            ->where('p.attendee = :attendee')
            ->andWhere('s.isActive = true')
            ->setParameter('attendee', $attendee->getUuid()->toBinary())
            ->orderBy('p.joinedAt', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
