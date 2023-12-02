<?php

namespace App\DataAccessLayer\Repository;

use App\Domain\Entity\Attendee;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @template-extends ServiceEntityRepository<Attendee>
 */
class AttendeeRepository extends ServiceEntityRepository
{
    private const MINI_IDENTIFIER_CHARACTERS = '01234566789ABCDEF';
    private const MINI_IDENTIFIER_MAX_ATTEMPTS = 10;


    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Attendee::class);
    }

    public function getFreeMiniIdentifier(): ?string {
        for ($i = 0; $i < self::MINI_IDENTIFIER_MAX_ATTEMPTS; $i++) {
            $charactersLength = strlen(self::MINI_IDENTIFIER_CHARACTERS);
            $randomString = '';
            for ($i = 0; $i < 4; $i++) {
                $randomString .= self::MINI_IDENTIFIER_CHARACTERS[random_int(0, $charactersLength - 1)];
            }

            if($this->findOneBy(['miniIdentifier' => $randomString]) === null) {
                return $randomString;
            }
        }

        return null;
    }
}
