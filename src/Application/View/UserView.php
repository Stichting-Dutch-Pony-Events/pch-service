<?php

namespace App\Application\View;

use DateTime;

class UserView
{
    public function __construct(
        public string   $id,
        public string   $name,
        public string   $username,
        public array    $roles,
        public DateTime $createdAt,
        public DateTime $updatedAt
    ) {
    }
}