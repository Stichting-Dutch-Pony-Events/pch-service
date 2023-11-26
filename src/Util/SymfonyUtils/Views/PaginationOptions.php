<?php

namespace App\Util\SymfonyUtils\Views;

class PaginationOptions
{
    public function __construct(
        public int $page,
        public int $limit,
        public ?int $offset = null
    ) {
    }
}
