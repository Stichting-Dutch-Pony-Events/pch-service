<?php

namespace App\Application\View\PublicTimetable;

use DateTime;

class TimetableItemView
{
    public function __construct(
        public string   $id,
        public string   $title,
        public DateTime $startTime,
        public DateTime $endTime,
        public string   $description,
        public ?string  $timetableLocationId,
        public ?string  $timetableDayId,
    ) {
    }
}