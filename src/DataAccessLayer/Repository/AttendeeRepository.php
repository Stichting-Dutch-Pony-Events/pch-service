<?php

namespace App\DataAccessLayer\Repository;

use App\Application\Request\AttendeeSearchRequest;
use App\Application\Response\AttendeeSearchResponse;
use App\Domain\Entity\Attendee;
use App\Util\SymfonyUtils\Exception\WrongTypeException;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use ReflectionException;
use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;
use Symfony\Component\Uid\Uuid;

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
            $randomString = '';
            for ($j = 0; $j < 10; $j++) {
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

    public function findAttendeeByIdentifier(string $identifier): ?Attendee
    {
        $qb = $this->createQueryBuilder('a');
        
        if (Uuid::isValid($identifier)) {
            $uuid = Uuid::fromRfc4122($identifier)->toBinary();
            $qb->where('a.id = :identifier')
                ->setParameter(':identifier', $uuid);
        } else {
            $qb->where('a.miniIdentifier = :identifier')
                ->orWhere('a.nfcTagId = :identifier')
                ->orWhere('a.ticketSecret = :identifier')
                ->setParameter(':identifier', $identifier);
        }

        return $qb
            ->getQuery()
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

    /**
     * @return Attendee[]
     */
    public function getAttendeesWithoutPrintJobs(): array
    {
        return $this->createQueryBuilder('a')
            ->leftJoin('a.printJobs', 'p')
            ->where('SIZE(a.printJobs) = 0')
            ->andWhere('a.nickName IS NOT NULL')
            ->getQuery()
            ->getResult();
    }

    private function getSortField(string $field): ?string
    {
        return match ($field) {
            'name' => 'a.name',
            'email' => 'a.email',
            'product.name' => 'p.name',
        };
    }

    /**
     * @throws ReflectionException
     * @throws WrongTypeException
     */
    public function searchAttendees(AttendeeSearchRequest $attendeeSearchRequest): AttendeeSearchResponse
    {
        $searchQuery = $this->buildSearchQuery(
            $this->createQueryBuilder('a')->select('a', 'p'),
            $attendeeSearchRequest
        );
        $countQuery = $this->buildSearchQuery(
            $this->createQueryBuilder('a')->select('COUNT(a.id)'),
            $attendeeSearchRequest,
            true
        );

        $totalItems = (int)$countQuery->getQuery()->getSingleScalarResult();
        $attendees = $searchQuery->getQuery()->getResult();

        return new AttendeeSearchResponse(
            items: $attendees,
            total: $totalItems,
            page: $attendeeSearchRequest->page,
            itemsPerPage: $attendeeSearchRequest->itemsPerPage
        );
    }

    public function buildSearchQuery(
        QueryBuilder          $qb,
        AttendeeSearchRequest $attendeeSearchRequest,
        bool                  $disableLimit = false
    ): QueryBuilder {
        $qb->leftJoin('a.product', 'p');

        $qb->where('a.name LIKE :query')
            ->orWhere('a.email LIKE :query')
            ->orWhere('a.nickName LIKE :query')
            ->setParameter('query', '%' . $attendeeSearchRequest->query . '%');

        if (!empty($attendeeSearchRequest->productId)) {
            $qb->andWhere('a.product = :productId')
                ->setParameter('productId', Uuid::fromRfc4122($attendeeSearchRequest->productId)->toBinary());
        }

        if ($attendeeSearchRequest->sortBy) {
            foreach ($attendeeSearchRequest->getSorts() as $sortItem) {
                $sortField = $this->getSortField($sortItem->key);
                if ($sortField) {
                    $qb->addOrderBy($sortField, $sortItem->direction);
                }
            }
        } else {
            $qb->addOrderBy('a.name', 'ASC');
        }

        if (!$disableLimit) {
            $qb->setFirstResult(($attendeeSearchRequest->page - 1) * $attendeeSearchRequest->itemsPerPage)
                ->setMaxResults($attendeeSearchRequest->itemsPerPage);
        }

        return $qb;
    }
}
