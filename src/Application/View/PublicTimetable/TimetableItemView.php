<?php

namespace App\Application\View\PublicTimetable;

use App\Application\View\Trait\EntityViewTrait;
use DateTime;

class TimetableItemView
{
    use EntityViewTrait;

    public function __construct(
        public string   $title,
        public DateTime $startTime,
        public DateTime $endTime,
        public string   $description,
        public ?string  $timetableLocationId,
        public ?string  $timetableDayId,
    ) {
    }
}