<?php

namespace App\Application\View;

use App\Application\View\Trait\EntityViewTrait;

class SettingView
{
    use EntityViewTrait;

    public function __construct(
        public ?string $name,
        public ?string $value,
    ) {
    }
}