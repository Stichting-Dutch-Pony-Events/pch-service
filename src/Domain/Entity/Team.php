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

    /** @var Collection<array-key, Attendee> $attendees */
    private Collection $attendees;

    /** @var Collection<array-key, CharacterQuizSubmissionTeamResult> $quizResults */
    private Collection $quizResults;

    /**
     * @param string $name
     * @param string $description
     * @param string $identifier
     * @param int $points
     * @param string $colour
     * @param Collection<array-key, Attendee>|null $attendees
     */
    public function __construct(
        private string $name,
        private string $description,
        private string $identifier,
        private int    $points = 0,
        private string $colour = '#ff9e5a',
        ?Collection    $attendees = null
    ) {
        $this->attendees = $attendees ?? new ArrayCollection();
        $this->quizResults = new ArrayCollection();
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

    public function getColour(): string
    {
        return $this->colour;
    }

    public function setColour(string $colour): self
    {
        $this->colour = $colour;

        return $this;
    }

    /** @return Collection<array-key, Attendee> */
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

    /**
     * @return Collection<array-key, CharacterQuizSubmissionTeamResult>
     */
    public function getQuizResults(): Collection
    {
        return $this->quizResults;
    }

    public function addQuizResult(CharacterQuizSubmissionTeamResult $quizResult): self
    {
        if (!$this->quizResults->contains($quizResult)) {
            $this->quizResults->add($quizResult);
        }

        return $this;
    }

    public function removeQuizResult(CharacterQuizSubmissionTeamResult $quizResult): self
    {
        $this->quizResults->removeElement($quizResult);

        return $this;
    }
}
