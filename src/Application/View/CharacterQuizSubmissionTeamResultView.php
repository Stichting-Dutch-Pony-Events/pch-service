<?php

namespace App\Application\View;

class CharacterQuizSubmissionTeamResultView
{
    public function __construct(
        public TeamView $team,
        public int      $points,
        public int      $percentage,
    ) {
    }
}