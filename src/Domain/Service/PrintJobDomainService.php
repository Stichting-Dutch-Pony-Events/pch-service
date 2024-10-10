<?php

namespace App\Domain\Service;

use App\Domain\Entity\Attendee;
use App\Domain\Entity\PrintJob;
use App\Domain\Enum\PrintJobStatusEnum;

readonly class PrintJobDomainService
{
    public function createPrintJob(Attendee $attendee): PrintJob
    {
        return new PrintJob(
            name: $attendee->getNickName(),
            productName: $attendee->getProduct()->getName(),
            attendee: $attendee,
            status: PrintJobStatusEnum::PENDING
        );
    }
}