<?php

namespace App\Application\View;

use App\Application\View\Trait\EntityViewTrait;
use DateTime;

class TimetableDayView
{
    use EntityViewTrait;

    public string $title;
    public DateTime $startsAt;
    public DateTime $endsAt;
    public int $order;
}