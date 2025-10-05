<?php

namespace App\Domain\Entity;

use App\Domain\Entity\Trait\HasUuidTrait;
use DateTime;
use Gedmo\Timestampable\Traits\Timestampable;
use Symfony\Component\Uid\Uuid;

class TimetableItem
{
    use HasUuidTrait, Timestampable;

    private Uuid $timetableLocationId;
    private Uuid $timetableDayId;

    public function __construct(
        private TimetableLocation $timetableLocation,
        private TimetableDay      $timetableDay,
        private string            $title,
        private DateTime          $startTime,
        private DateTime          $endTime,
        private string            $colour = '#ff9e5a',
        private ?string           $description = null,
        private ?Attendee         $volunteer = null,
    ) {
        $this->timetableLocation->addTimetableItem($this);
        $this->timetableDay->addTimetableItem($this);
        $this->volunteer?->addTimetableItem($this);
        $this->timetableLocationId = $this->timetableLocation->getUuid();
        $this->timetableDayId = $this->timetableDay->getUuid();
    }

    public function getTimetableLocationId(): string
    {
        return $this->timetableLocationId->toRfc4122();
    }

    public function getTimetableLocationUuid(): Uuid
    {
        return $this->timetableLocationId;
    }

    public function getTimetableDayId(): string
    {
        return $this->timetableDayId->toRfc4122();
    }

    public function getTimetableDayUuid(): Uuid
    {
        return $this->timetableDayId;
    }

    public function getTimetableLocation(): TimetableLocation
    {
        return $this->timetableLocation;
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

    public function getColour(): string
    {
        return $this->colour;
    }

    public function setColour(string $colour): self
    {
        $this->colour = $colour;
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