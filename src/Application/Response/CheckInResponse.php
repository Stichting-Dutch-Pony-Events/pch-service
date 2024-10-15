<?php

namespace App\Application\Response;

use App\Application\Enum\BadgeSourceEnum;
use App\Application\View\AttendeeView;
use App\DataAccessLayer\Pretix\Enum\CheckInErrorReason;
use App\DataAccessLayer\Pretix\Enum\CheckInStatus;

class CheckInResponse
{
    public function __construct(
        public CheckInStatus       $status,
        public ?CheckInErrorReason $errorReason = null,
        public ?string             $reasonExplanation = null,
        public bool                $requiresAttention = false,
        public ?AttendeeView       $attendee = null,
        public BadgeSourceEnum     $badgeSource = BadgeSourceEnum::BADGE_STASH
    ) {
    }
}
