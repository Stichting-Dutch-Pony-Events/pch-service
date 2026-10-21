<?php

namespace App\Domain\Entity;

use App\Domain\Entity\Trait\HasUuidTrait;
use DateTime;
use Gedmo\Timestampable\Traits\Timestampable;

class LiveQuizParticipant
{
    use Timestampable, HasUuidTrait;

    public function __construct(
        private LiveQuizSession $session,
        private Attendee        $attendee,
        private DateTime        $joinedAt,
    ) {
    }

    public function getSession(): LiveQuizSession
    {
        return $this->session;
    }

    public function getSessionId(): string
    {
        return $this->session->getId();
    }

    public function getAttendee(): Attendee
    {
        return $this->attendee;
    }

    public function getAttendeeId(): string
    {
        return $this->attendee->getId();
    }

    public function getJoinedAt(): DateTime
    {
        return $this->joinedAt;
    }
}
