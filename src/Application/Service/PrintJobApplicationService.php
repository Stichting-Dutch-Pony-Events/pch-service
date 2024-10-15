<?php

namespace App\Application\Service;

use App\Application\Request\SetPrintJobStatusRequest;
use App\Domain\Entity\Attendee;
use App\Domain\Entity\PrintJob;
use App\Domain\Enum\PrintJobStatusEnum;
use App\Domain\Service\PrintJobDomainService;
use App\Util\BadgeGenerator;
use App\Util\Exceptions\Exception\Common\InvalidInputException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Filesystem\Filesystem;

readonly class PrintJobApplicationService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private BadgeGenerator         $badgeGenerator,
        private Filesystem             $filesystem,
        private PrintJobDomainService  $printJobDomainService
    ) {
    }

    public function createPrintJob(Attendee $attendee): PrintJob
    {
        return $this->entityManager->wrapInTransaction(function () use ($attendee): PrintJob {
            if ($attendee->getBadgeFile() === null || !$this->filesystem->exists($attendee->getBadgeFile())) {
                $this->badgeGenerator->generate($attendee);
            }

            $printJob = $this->printJobDomainService->createPrintJob($attendee);
            $this->entityManager->persist($printJob);

            return $printJob;
        });
    }

    public function setPrintJobStatus(PrintJob $printJob, SetPrintJobStatusRequest $setPrintJobStatusRequest): PrintJob
    {
        return $this->entityManager->wrapInTransaction(function () use ($printJob, $setPrintJobStatusRequest): PrintJob {
            if($printJob->getStatus() === $setPrintJobStatusRequest->status) {
                throw new InvalidInputException("Print job already has this status");
            }

            if($setPrintJobStatusRequest->status === PrintJobStatusEnum::PENDING && $printJob->getStatus() === PrintJobStatusEnum::PRINTING) {
                throw new InvalidInputException("PrintJob status can't go from Printing to Pending");
            }

            if($printJob->getStatus() === PrintJobStatusEnum::COMPLETED) {
                throw new InvalidInputException("PrintJob status can't go from Completed to anything else");
            }

            $printJob->setStatus($setPrintJobStatusRequest->status);

            return $printJob;
        });
    }
}