<?php

namespace App\Application\View;

use App\Application\View\Trait\EntityViewTrait;

class LiveQuizSessionView
{
    use EntityViewTrait;

    public string $name;
    public bool $isActive;
    public ?string $password = null;
}
