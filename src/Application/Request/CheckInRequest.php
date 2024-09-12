<?php

namespace App\Application\Request;

use App\Domain\Enum\CheckInListType;

class CheckInRequest
{
    public function __construct(
        public string $secret,
        public CheckInListType $listType = CheckInListType::TICKET,
        public bool $merchPreCheckIn = false,
    )
    {
    }
}
