<?php

namespace App\Application\View;

use DateTime;

class AttendeeAchievementView
{
    public function __construct(
        public string   $id,
        public string   $achievementId,
        public DateTime $createdAt,
    ) {
    }
}