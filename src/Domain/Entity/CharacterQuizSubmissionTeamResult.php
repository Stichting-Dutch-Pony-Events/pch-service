<?php

namespace App\Domain\Entity;

use App\Domain\Entity\Trait\HasUuidTrait;
use Gedmo\Timestampable\Traits\Timestampable;

class CharacterQuizSubmissionTeamResult
{
    use HasUuidTrait, Timestampable;

    public function __construct(
        private Team                    $team,
        private int                     $points,
        private int                     $percentage,
        private CharacterQuizSubmission $submission
    ) {
        $this->submission->addTeamResult($this);
    }

    public function getTeam(): Team
    {
        return $this->team;
    }

    public function setTeam(Team $team): CharacterQuizSubmissionTeamResult
    {
        $this->team = $team;
        return $this;
    }

    public function getPoints(): int
    {
        return $this->points;
    }

    public function setPoints(int $points): CharacterQuizSubmissionTeamResult
    {
        $this->points = $points;
        return $this;
    }

    public function getPercentage(): int
    {
        return $this->percentage;
    }

    public function setPercentage(int $percentage): CharacterQuizSubmissionTeamResult
    {
        $this->percentage = $percentage;
        return $this;
    }

    public function getSubmission(): CharacterQuizSubmission
    {
        return $this->submission;
    }
}