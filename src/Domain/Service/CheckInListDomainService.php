<?php

namespace App\Domain\Service;

use App\Application\Request\CheckInListRequest;
use App\Domain\Entity\CheckInList;

class CheckInListDomainService
{
    public function createCheckInList(CheckInListRequest $checkInListRequest): CheckInList
    {
        return new CheckInList(
            name: $checkInListRequest->name,
            pretixId: $checkInListRequest->pretixId,
            startTime: $checkInListRequest->startTime,
            endTime: $checkInListRequest->endTime,
            type: $checkInListRequest->type,
            pretixProductIds: $checkInListRequest->pretixProductIds,
        );
    }
}
