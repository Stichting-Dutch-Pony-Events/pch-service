<?php

namespace App\Application\View;

use DateTime;

class AttendeeView
{
    public function __construct(
        public ?string     $id,
        public string      $name,
        public ?string     $firstName,
        public ?string     $middleName,
        public ?string     $familyName,
        public ?string     $nickName,
        public ?string     $email,
        public string      $orderCode,
        public int         $ticketId,
        public ProductView $product,
        public DateTime    $createdAt,
        public DateTime    $updatedAt,
    ) {
    }
}
