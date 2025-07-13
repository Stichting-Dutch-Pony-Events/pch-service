<?php

namespace App\Application\Request;

use DateTime;

class TimetableDayRequest
{
    public function __construct(
        public string   $title,
        public DateTime $startsAt,
        public DateTime $endsAt,
    ) {
    }
}