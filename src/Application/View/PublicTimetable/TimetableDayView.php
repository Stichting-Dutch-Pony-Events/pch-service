<?php

namespace App\Application\View\PublicTimetable;

use App\Application\View\Trait\EntityViewTrait;
use DateTime;

class TimetableDayView
{
    use EntityViewTrait;

    /**
     * @param string[] $timetableLocationIds
     */
    public function __construct(
        public string   $title,
        public DateTime $startsAt,
        public DateTime $endsAt,
        public int      $order,
        public array    $timetableLocationIds
    ) {
    }
}