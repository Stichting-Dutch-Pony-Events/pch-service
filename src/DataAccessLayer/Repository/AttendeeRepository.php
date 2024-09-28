<?php

namespace App\DataAccessLayer\Repository;

use App\Domain\Entity\Attendee;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @template-extends ServiceEntityRepository<Attendee>
 */
class AttendeeRepository extends ServiceEntityRepository implements UserLoaderInterface
{
    private const MINI_IDENTIFIER_CHARACTERS = '01234566789ABCDEF';
    private const MINI_IDENTIFIER_MAX_ATTEMPTS = 10;


    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Attendee::class);
    }

    public function getFreeMiniIdentifier(): ?string
    {
        for ($i = 0; $i < self::MINI_IDENTIFIER_MAX_ATTEMPTS; $i++) {
            $charactersLength = strlen(self::MINI_IDENTIFIER_CHARACTERS);
            $randomString     = '';
            for ($j = 0; $j < 4; $j++) {
                $randomString .= self::MINI_IDENTIFIER_CHARACTERS[random_int(0, $charactersLength - 1)];
            }

            if ($this->findOneBy(['miniIdentifier' => $randomString]) === null) {
                return $randomString;
            }
        }

        return null;
    }

    public function loadUserByIdentifier(string $identifier): ?Attendee
    {
        $entityManager = $this->getEntityManager();

        return $entityManager->createQuery(
                'SELECT u
                FROM App\Domain\Entity\Attendee u
                WHERE u.miniIdentifier = :identifier
                OR u.nfcTagId = :identifier'
            )
            ->setParameter('identifier', $identifier)
            ->getOneOrNullResult();
    }

    /**
     * @return Attendee[]
     */
    public function getAttendeesWithoutTeam(): array
    {
        return $this->createQueryBuilder('a')
            ->select('a')
            ->where('a.team IS NULL')
            ->getQuery()
            ->execute();
    }
}
