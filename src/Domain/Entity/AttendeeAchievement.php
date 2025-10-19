<?php

namespace App\Domain\Entity;

use App\Domain\Entity\Trait\HasUuidTrait;
use Gedmo\Timestampable\Traits\Timestampable;

class AttendeeAchievement
{
    use Timestampable, HasUuidTrait;

    public function __construct(
        private Achievement $achievement,
        private Attendee    $attendee,
    ) {
        $this->attendee->addAchievement($this);
    }

    public function getAchievement(): Achievement
    {
        return $this->achievement;
    }

    public function getAchievementId(): ?string
    {
        return $this->getAchievement()->getId();
    }

    public function getAttendee(): Attendee
    {
        return $this->attendee;
    }
}