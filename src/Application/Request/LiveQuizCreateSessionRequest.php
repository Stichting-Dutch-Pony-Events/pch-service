<?php

namespace App\Application\Request;

class LiveQuizCreateSessionRequest
{
    public function __construct(
        public string $name,
        public string $password,
    ) {
    }
}
