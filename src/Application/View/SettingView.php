<?php

namespace App\Application\View;

class SettingView
{
    public function __construct(
        public ?string $id,
        public ?string $name,
        public ?string $value,
    ) {
    }
}