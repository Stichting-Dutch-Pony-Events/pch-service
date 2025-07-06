<?php

namespace App\Application\View;

use App\Application\View\Trait\EntityViewTrait;

class QuizAnswerView
{
    use EntityViewTrait;

    public string $answer;
    public int $order;
}