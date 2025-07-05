<?php

namespace App\Application\View;

use App\Application\View\Trait\EntityViewTrait;

class AchievementView
{
    use EntityViewTrait;

    public function __construct(
        public string $name,
        public string $description,
        public string $identifier,
        public int    $points,
        public bool   $eveningActivity,
        public bool   $hasUnlockCode
    ) {
    }
}