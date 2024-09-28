<?php

namespace App\Domain\Entity;

use Gedmo\Timestampable\Traits\Timestampable;
use Symfony\Component\Uid\Uuid;

class AttendeeAchievement
{
    use Timestampable;

    private ?Uuid $id = null;

    public function __construct(
        private Achievement $achievement,
        private Attendee    $attendee,
    ) {
    }

    public function getId(): ?string
    {
        return $this->id?->toRfc4122();
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