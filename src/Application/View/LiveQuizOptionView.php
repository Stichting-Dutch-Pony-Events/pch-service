<?php

namespace App\Application\View;

use App\Application\View\Trait\EntityViewTrait;

class LiveQuizOptionView
{
    use EntityViewTrait;

    public string $label;
    public int $order;
}
