<?php

namespace App\Application\View;

use App\Application\View\Trait\EntityViewTrait;

class QuizAnswerTeamWeightView
{
    use EntityViewTrait;

    public TeamView $team;
    public int $weight;
}