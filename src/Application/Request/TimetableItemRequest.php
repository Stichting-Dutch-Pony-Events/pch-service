<?php

namespace App\Application\Request;

use DateTime;

class TimetableItemRequest
{
    public function __construct(
        public string   $timetableLocationId,
        public string   $timetableDayId,
        public string   $title,
        public DateTime $startTime,
        public DateTime $endTime,
        public ?string  $description = null,
        public ?string  $volunteerId = null,
    ) {
    }
}