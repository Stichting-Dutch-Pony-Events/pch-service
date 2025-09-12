<?php

namespace App\Application\Response;

use App\Application\View\PublicTimetable\TimetableDayView;
use App\Application\View\PublicTimetable\TimetableLocationView;

class PublicTimetableResponse
{
    /**
     * @param TimetableDayView[] $timetableDays
     * @param TimetableLocationView[] $timetableLocations
     */
    public function __construct(
        public array $timetableDays = [],
        public array $timetableLocations = [],
    ) {
    }
}