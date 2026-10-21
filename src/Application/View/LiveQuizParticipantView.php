<?php

namespace App\Application\View;

use App\Application\View\Trait\EntityViewTrait;
use DateTime;

class LiveQuizParticipantView
{
    use EntityViewTrait;

    public string $sessionId;
    public string $attendeeId;
    public DateTime $joinedAt;
}
