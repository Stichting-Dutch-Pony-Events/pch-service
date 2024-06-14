<?php

namespace App\Application\Request;

class SetPasswordRequest
{
    public function __construct(
        public string $currentPassword,
        public string $password,
        public string $passwordConfirmation,
    ) {
    }
}