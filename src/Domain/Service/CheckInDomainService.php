<?php

namespace App\Domain\Service;

use App\Application\Response\CheckInResponse;
use App\Domain\Entity\Attendee;
use App\Domain\Entity\CheckIn;
use App\Domain\Entity\CheckInList;
use Carbon\Carbon;

class CheckInDomainService
{
    public function createCheckIn(CheckInResponse $checkInResponse, Attendee $attendee, CheckInList $checkInList): ?CheckIn
    {
        return new CheckIn(
            $attendee,
            $checkInList,
            $checkInResponse->status,
            $checkInResponse->errorReason,
            $checkInResponse->reasonExplanation,
            Carbon::now()
        );
    }
}
