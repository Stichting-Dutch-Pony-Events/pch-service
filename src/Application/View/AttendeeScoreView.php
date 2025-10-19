<?php

namespace App\Application\View;

class AttendeeScoreView
{
    public function __construct(
        public ?string $nickName,
        public int     $points,
        public int     $achievementsCompletedTime,
        public int     $position,
    ) {
    }
}