<?php

namespace App\Domain\Entity;

use App\Domain\Entity\Trait\HasUuidTrait;
use App\Domain\Enum\TimetableLocationType;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Gedmo\Timestampable\Traits\Timestampable;

class TimetableLocation
{
    use HasUuidTrait, Timestampable;

    /** @var Collection<array-key, TimetableDay> $timetableDays */
    private Collection $timetableDays;

    /** @var Collection<array-key, TimetableItem> $timetableItems */
    private Collection $timetableItems;

    /**
     * @param string $title
     * @param TimetableLocationType $timetableLocationType
     * @param int $order
     * @param Collection<array-key, TimetableDay>|null $timetableDays
     */
    public function __construct(
        private string                $title,
        private TimetableLocationType $timetableLocationType,
        private int                   $order = 0,
        ?Collection                   $timetableDays = null
    ) {
        $this->timetableDays = $timetableDays ?? new ArrayCollection();
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

    public function getTimetableLocationType(): TimetableLocationType
    {
        return $this->timetableLocationType;
    }

    public function setTimetableLocationType(TimetableLocationType $timetableLocationType): self
    {
        $this->timetableLocationType = $timetableLocationType;
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
     * @return Collection<array-key, TimetableDay>
     */
    public function getTimetableDays(): Collection
    {
        return $this->timetableDays;
    }

    public function addTimetableDay(TimetableDay $timetableDay): self
    {
        if (!$this->timetableDays->contains($timetableDay)) {
            $this->timetableDays->add($timetableDay);
            $timetableDay->addTimetableLocation($this);
        }

        return $this;
    }

    public function removeTimetableDay(TimetableDay $timetableDay): self
    {
        if ($this->timetableDays->contains($timetableDay)) {
            $this->timetableDays->removeElement($timetableDay);
            $timetableDay->removeTimetableLocation($this);
        }

        return $this;
    }

    public function clearTimetableDays(): self
    {
        foreach ($this->getTimetableDays() as $timetableDay) {
            $this->removeTimetableDay($timetableDay);
        }

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
        if (!$this->timetableItems->contains($timetableItem) && $timetableItem->getTimetableLocation() === $this) {
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