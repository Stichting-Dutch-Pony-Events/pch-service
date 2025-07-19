<?php

namespace App\Application\Request;

use App\Domain\Enum\TimetableLocationType;

class TimetableLocationRequest
{
    /**
     * @param string $title
     * @param TimetableLocationType $timetableLocationType
     * @param string[] $timetableDays
     */
    public function __construct(
        public string                $title,
        public TimetableLocationType $timetableLocationType,
        public array                 $timetableDays,
    ) {
    }
}