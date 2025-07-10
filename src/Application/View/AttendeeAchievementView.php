<?php

namespace App\Application\View;

use App\Application\View\Trait\EntityViewTrait;

class AttendeeAchievementView
{
    use EntityViewTrait;

    public function __construct(
        public string $achievementId,
    ) {
    }
}