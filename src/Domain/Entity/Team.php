<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use App\Domain\Entity\Trait\HasUuidTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Gedmo\Timestampable\Traits\Timestampable;

class Team
{
    use Timestampable, HasUuidTrait;

    /** @var Collection<int, Attendee> $attendees */
    private Collection $attendees;

    public function __construct(
        private string $name,
        private string $description,
        private string $identifier,
        private int    $points = 0,
        /** @var Collection<int, Attendee> $attendees */
        ?Collection    $attendees = null
    ) {
        $this->attendees = $attendees ?? new ArrayCollection();
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    public function setIdentifier(string $identifier): self
    {
        $this->identifier = $identifier;

        return $this;
    }

    public function getPoints(): int
    {
        return $this->points;
    }

    public function setPoints(int $points): self
    {
        $this->points = $points;

        return $this;
    }

    /** @return Collection<int, Attendee> */
    public function getAttendees(): Collection
    {
        return $this->attendees;
    }

    public function addAttendee(Attendee $attendee): self
    {
        if (!$this->attendees->contains($attendee)) {
            $this->attendees->add($attendee);
        }

        return $this;
    }

    public function removeAttendee(Attendee $attendee): self
    {
        $this->attendees->removeElement($attendee);

        return $this;
    }
}
