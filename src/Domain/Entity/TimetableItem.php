<?php

namespace App\Domain\Entity;

use App\Domain\Entity\Trait\HasUuidTrait;
use DateTime;
use Gedmo\Timestampable\Traits\Timestampable;

class TimetableItem
{
    use HasUuidTrait, Timestampable;

    public function __construct(
        private TimetableLocation $timetableLocation,
        private TimetableDay      $timetableDay,
        private string            $title,
        private DateTime          $startTime,
        private DateTime          $endTime,
        private ?string           $description = null,
        private ?Attendee         $volunteer = null,
    ) {
        $this->timetableLocation->addTimetableItem($this);
        $this->timetableDay->addTimetableItem($this);
        $this->volunteer?->addTimetableItem($this);
    }

    public function getTimetableLocation(): TimetableLocation
    {
        return $this->timetableLocation;
    }

    public function getTimetableLocationId(): string
    {
        return $this->timetableLocation->getId();
    }

    public function getTimetableDay(): TimetableDay
    {
        return $this->timetableDay;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function getStartTime(): DateTime
    {
        return $this->startTime;
    }

    public function setStartTime(DateTime $startTime): self
    {
        $this->startTime = $startTime;
        return $this;
    }

    public function getEndTime(): DateTime
    {
        return $this->endTime;
    }

    public function setEndTime(DateTime $endTime): self
    {
        $this->endTime = $endTime;
        return $this;
    }

    public function getVolunteer(): ?Attendee
    {
        return $this->volunteer;
    }

    public function setVolunteer(?Attendee $volunteer): self
    {
        $this->volunteer?->removeTimetableItem($this);
        $this->volunteer = $volunteer;
        $this->volunteer?->addTimetableItem($this);
        return $this;
    }
}