<?php

namespace App\Application\Request;

class AttendeeRequest
{
    public function __construct(
        public string  $name,
        public ?string $firstName,
        public ?string $middleName,
        public ?string $familyName,
        public ?string $nickName,
        public ?string $email,
        public string  $orderCode,
        public int     $ticketId,
        public string  $ticketSecret,
        public string  $productId,
        public ?string $nfcTagId = null,
        public ?string $miniIdentifier = null,
        public ?string $pinCode = null,
    ) {
    }
}
