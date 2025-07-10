<?php

namespace App\Application\Request;

class TeamRequest
{
    public function __construct(
        public string $name,
        public string $description,
        public string $identifier,
    ) {
    }
}