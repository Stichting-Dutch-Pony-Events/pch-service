<?php

namespace App\Application\Request;

class SortItem
{
    public function __construct(
        public string $key,
        public string $direction = 'asc'
    ) {
    }
}