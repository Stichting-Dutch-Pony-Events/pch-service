<?php

namespace App\DataAccessLayer\Repository;

use App\Domain\Entity\PrintJob;
use App\Domain\Enum\PrintJobStatusEnum;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PrintJob>
 * @template-extends ServiceEntityRepository<PrintJob>
 */
class PrintJobRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PrintJob::class);
    }

    /**
     * @return PrintJob[]
     */
    public function getPrintablePrintJobs(): array
    {
        return $this->createQueryBuilder('pj')
            ->select('pj')
            ->where('pj.status = :statusPending')
            ->orderBy('pj.productName', 'asc')
            ->addOrderBy('pj.name', 'asc')
            ->leftJoin('pj.attendee', 'a')
            ->setParameter('statusPending', PrintJobStatusEnum::PENDING->value)
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();
    }
}