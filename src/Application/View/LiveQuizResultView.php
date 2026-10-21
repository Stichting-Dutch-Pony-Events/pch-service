<?php

namespace App\Application\View;

class LiveQuizResultView
{
    public string $questionId;
    public int $totalResponses;

    /** @var LiveQuizOptionResultView[] */
    public array $options;
}
