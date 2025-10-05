<?php

namespace App\Domain\Entity;

use App\Domain\Entity\Trait\HasUuidTrait;
use Gedmo\Timestampable\Traits\Timestampable;

class CharacterQuizSubmissionAnswer
{
    use HasUuidTrait, Timestampable;

    public function __construct(
        private QuizQuestion            $question,
        private QuizAnswer              $answer,
        private CharacterQuizSubmission $submission
    ) {
        $this->submission->addAnswer($this);
    }

    public function getQuestion(): QuizQuestion
    {
        return $this->question;
    }

    public function getAnswer(): QuizAnswer
    {
        return $this->answer;
    }

    public function getSubmission(): CharacterQuizSubmission
    {
        return $this->submission;
    }
}