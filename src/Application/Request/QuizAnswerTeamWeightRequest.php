<?php

namespace App\Application\Request;

class QuizAnswerTeamWeightRequest
{
    public function __construct(
        public ?string $id,
        public string  $teamId,
        public int     $weight
    ) {
    }
}