<?php

namespace App\Application\View;

use App\Application\View\Trait\EntityViewTrait;

class ProductView
{
    use EntityViewTrait;

    public function __construct(
        public string $name,
        public int    $pretixId,
    ) {
    }
}
