<?php

namespace App\Application\View;

class AchievementView
{
    public function __construct(
        public string $id,
        public string $name,
        public string $description,
        public string $identifier,
        public int    $points,
        public bool   $eveningActivity
    ) {
    }
}