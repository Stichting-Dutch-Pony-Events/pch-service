<?php

namespace App\Application\Request;

class ProductRequest
{
    public function __construct(
        public string $name,
        public int $pretixId,
    )
    {
    }
}
