<?php

namespace App\Domain\Entity;

use App\Domain\Entity\Trait\HasUuidTrait;
use Gedmo\Timestampable\Traits\Timestampable;

class QuizAnswerTeamWeight
{
    use HasUuidTrait, Timestampable;

    public function __construct(
        private QuizAnswer $quizAnswer,
        private Team       $team,
        private int        $weight,
    ) {
        $this->quizAnswer->addQuizAnswerTeamWeight($this);
    }

    public function getQuizAnswer(): QuizAnswer
    {
        return $this->quizAnswer;
    }

    public function getTeam(): Team
    {
        return $this->team;
    }

    public function setTeam(Team $team): self
    {
        $this->team = $team;
        return $this;
    }

    public function getWeight(): int
    {
        return $this->weight;
    }

    public function setWeight(int $weight): self
    {
        $this->weight = $weight;
        return $this;
    }


}