<?php

namespace App\Application\Request;

use App\Domain\Enum\TShirtSize;

class AttendeeRequest
{
    public function __construct(
        public string      $name,
        public ?string     $firstName,
        public ?string     $middleName,
        public ?string     $familyName,
        public ?string     $nickName,
        public ?string     $email,
        public string      $orderCode,
        public int         $ticketId,
        public ?string     $ticketSecret,
        public string      $productId,
        public ?string     $nfcTagId = null,
        public ?string     $miniIdentifier = null,
        public ?TShirtSize $tShirtSize = null,
        public ?string     $fireBaseToken = null,
        public ?string     $overrideBadgeProductId = null,
    ) {
    }
}
