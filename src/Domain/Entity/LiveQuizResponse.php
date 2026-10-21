<?php

namespace App\Domain\Entity;

use App\Domain\Entity\Trait\HasUuidTrait;
use Gedmo\Timestampable\Traits\Timestampable;

class LiveQuizResponse
{
    use Timestampable, HasUuidTrait;

    public function __construct(
        private LiveQuizQuestion    $question,
        private LiveQuizOption      $option,
        private LiveQuizParticipant $participant,
    ) {
    }

    public function getQuestion(): LiveQuizQuestion
    {
        return $this->question;
    }

    public function getOption(): LiveQuizOption
    {
        return $this->option;
    }

    public function getParticipant(): LiveQuizParticipant
    {
        return $this->participant;
    }
}
