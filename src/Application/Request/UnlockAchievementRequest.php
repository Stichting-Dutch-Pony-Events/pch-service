<?php

namespace App\Application\Request;

class UnlockAchievementRequest
{
    public function __construct(
        public string $unlockCode
    ) {
    }
}