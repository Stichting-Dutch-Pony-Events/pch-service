<?php

namespace App\Application\Request;

class LiveQuizJoinSessionRequest
{
    public function __construct(
        public string $password,
    ) {
    }
}
