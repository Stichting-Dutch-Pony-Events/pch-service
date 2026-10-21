<?php

namespace App\Application\Request;

class LiveQuizRespondRequest
{
    public function __construct(
        public string $optionId,
    ) {
    }
}
