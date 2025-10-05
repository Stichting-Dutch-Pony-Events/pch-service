<?php

namespace App\Domain\Entity;

use App\Domain\Entity\Trait\HasUuidTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Gedmo\Timestampable\Traits\Timestampable;

class CharacterQuizSubmission
{
    use HasUuidTrait, Timestampable;

    /** @var Collection<array-key, CharacterQuizSubmissionAnswer> $answers */
    private Collection $answers;

    /** @var Collection<array-key, CharacterQuizSubmissionTeamResult> $teamResults */
    private Collection $teamResults;

    public function __construct(
        private Attendee $attendee,
    ) {
        $this->answers = new ArrayCollection();
        $this->teamResults = new ArrayCollection();

        $this->attendee->addCharacterQuizSubmission($this);
    }

    /**
     * @return Collection<array-key, CharacterQuizSubmissionAnswer>
     */
    public function getAnswers(): Collection
    {
        return $this->answers;
    }

    public function addAnswer(CharacterQuizSubmissionAnswer $answer): self
    {
        if (!$this->answers->contains($answer)) {
            $this->answers->add($answer);
        }

        return $this;
    }

    public function removeAnswer(CharacterQuizSubmissionAnswer $answer): self
    {
        if ($this->answers->contains($answer)) {
            $this->answers->removeElement($answer);
        }

        return $this;
    }

    /**
     * @return Collection<array-key, CharacterQuizSubmissionTeamResult>
     */
    public function getTeamResults(): Collection
    {
        return $this->teamResults;
    }

    public function addTeamResult(CharacterQuizSubmissionTeamResult $teamResults): self
    {
        if (!$this->teamResults->contains($teamResults)) {
            $this->teamResults->add($teamResults);
        }

        return $this;
    }

    public function removeTeamResult(CharacterQuizSubmissionTeamResult $teamResults): self
    {
        if ($this->teamResults->contains($teamResults)) {
            $this->teamResults->removeElement($teamResults);
        }

        return $this;
    }

    public function getAttendee(): Attendee
    {
        return $this->attendee;
    }

    public function setAttendee(Attendee $attendee): self
    {
        $this->attendee = $attendee;
        return $this;
    }
}