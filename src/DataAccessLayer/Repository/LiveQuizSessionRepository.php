<?php

namespace App\DataAccessLayer\Repository;

use App\Domain\Entity\LiveQuizSession;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<LiveQuizSession>
 */
class LiveQuizSessionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LiveQuizSession::class);
    }

    public function findActiveByPassword(string $password): ?LiveQuizSession
    {
        $session = $this->findOneBy(['isActive' => true, 'password' => $password]);
        if ($session !== null) {
            return $session;
        }

        // Sessions created before plain-text storage hold a bcrypt hash.
        foreach ($this->findBy(['isActive' => true]) as $session) {
            if (LiveQuizSession::isHashedPassword($session->getPassword())
                && password_verify($password, $session->getPassword())) {
                return $session;
            }
        }

        return null;
    }
}
