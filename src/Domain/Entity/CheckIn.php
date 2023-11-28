<?php

namespace App\Domain\Entity;

use App\DataAccessLayer\Pretix\Enum\CheckInErrorReason;
use App\DataAccessLayer\Pretix\Enum\CheckInStatus;
use DateTime;
use Gedmo\Timestampable\Traits\Timestampable;
use Symfony\Component\Uid\Uuid;

class CheckIn
{
    use Timestampable;

    private ?Uuid $id = null;

    public function __construct(
        private Attendee            $attendee,
        private CheckInList         $checkInList,
        private CheckInStatus       $status,
        private ?CheckInErrorReason $errorReason,
        private ?string             $reasonExplanation,
        private DateTime            $checkInTime,
    ) {
    }

    public function getId(): ?string
    {
        return $this->id?->toRfc4122();
    }

    public function getAttendee(): Attendee
    {
        return $this->attendee;
    }

    public function getCheckInList(): CheckInList
    {
        return $this->checkInList;
    }

    public function getStatus(): CheckInStatus
    {
        return $this->status;
    }

    public function getErrorReason(): ?CheckInErrorReason
    {
        return $this->errorReason;
    }

    public function getReasonExplanation(): ?string
    {
        return $this->reasonExplanation;
    }

    public function getCheckInTime(): DateTime
    {
        return $this->checkInTime;
    }
}
