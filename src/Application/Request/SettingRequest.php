<?php

namespace App\Application\Request;

class SettingRequest
{
    public function __construct(
        public string $name,
        public string $value
    ) {
    }
}