<?php

namespace App\Application\Request;

class AwardAchievementRequest
{
    public function __construct(
        public string $attendeeIdentifier,
    ) {
    }
}