<?php

namespace App\Application\View;

class TeamView
{
    public function __construct(
        public ?string $id,
        public string  $name,
        public string  $description,
        public string  $identifier
    ) {
    }
}