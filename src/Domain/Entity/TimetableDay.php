<?php

namespace App\Domain\Entity;

use App\Domain\Entity\Trait\HasUuidTrait;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Gedmo\Timestampable\Traits\Timestampable;

class TimetableDay
{
    use HasUuidTrait, Timestampable;

    /** @var Collection<array-key, TimetableLocation> $timetableLocations */
    private Collection $timetableLocations;

    /** @var Collection<array-key, TimetableItem> $timetableItems */
    private Collection $timetableItems;

    /**
     * @param string $title
     * @param DateTime $startsAt
     * @param DateTime $endsAt
     * @param int $order
     * @param Collection<array-key, TimetableLocation>|null $timetableLocations
     */
    public function __construct(
        private string   $title,
        private DateTime $startsAt,
        private DateTime $endsAt,
        private int      $order = 0,
        ?Collection      $timetableLocations = null,
    ) {
        $this->timetableLocations = $timetableLocations ?? new ArrayCollection();
        $this->timetableItems = new ArrayCollection();
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

    public function getStartsAt(): DateTime
    {
        return $this->startsAt;
    }

    public function setStartsAt(DateTime $startsAt): self
    {
        $this->startsAt = $startsAt;
        return $this;
    }

    public function getEndsAt(): DateTime
    {
        return $this->endsAt;
    }

    public function setEndsAt(DateTime $endsAt): self
    {
        $this->endsAt = $endsAt;
        return $this;
    }

    public function getOrder(): int
    {
        return $this->order;
    }

    public function setOrder(int $order): self
    {
        $this->order = $order;
        return $this;
    }

    /**
     * @return Collection<array-key, TimetableLocation>
     */
    public function getTimetableLocations(): Collection
    {
        return $this->timetableLocations;
    }

    /**
     * @return string[]
     */
    public function getTimetableLocationIds(): array
    {
        return $this->timetableLocations->map(static fn(TimetableLocation $location) => $location->getId())->toArray();
    }

    public function addTimetableLocation(TimetableLocation $timetableLocation): self
    {
        if (!$this->timetableLocations->contains($timetableLocation)) {
            $this->timetableLocations->add($timetableLocation);
        }

        return $this;
    }

    public function removeTimetableLocation(TimetableLocation $timetableLocation): self
    {
        if ($this->timetableLocations->contains($timetableLocation)) {
            $this->timetableLocations->removeElement($timetableLocation);
        }

        return $this;
    }

    public function clearTimetableLocations(): self
    {
        $this->timetableLocations->clear();

        return $this;
    }

    /**
     * @return Collection<array-key, TimetableItem>
     */
    public function getTimetableItems(): Collection
    {
        return $this->timetableItems;
    }

    public function addTimetableItem(TimetableItem $timetableItem): self
    {
        if (!$this->timetableItems->contains($timetableItem) && $timetableItem->getTimetableDay() === $this) {
            $this->timetableItems->add($timetableItem);
        }

        return $this;
    }

    public function removeTimetableItem(TimetableItem $timetableItem): self
    {
        if ($this->timetableItems->contains($timetableItem)) {
            $this->timetableItems->removeElement($timetableItem);
        }

        return $this;
    }
}