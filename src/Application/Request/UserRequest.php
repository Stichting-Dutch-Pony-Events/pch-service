<?php

namespace App\Application\Request;

class UserRequest
{
    public function __construct(
        public string $name,
        public string $username,
        public ?string $password,
        public ?array  $roles,
    ) {
    }
}
