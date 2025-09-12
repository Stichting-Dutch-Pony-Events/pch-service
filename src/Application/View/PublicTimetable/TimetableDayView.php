<?php

namespace App\Application\View\PublicTimetable;

use DateTime;

class TimetableDayView
{
    /**
     * @param string[] $timetableLocationIds
     */
    public function __construct(
        public string   $id,
        public string   $title,
        public DateTime $startsAt,
        public DateTime $endsAt,
        public int      $order,
        public array    $timetableLocationIds
    ) {
    }
}