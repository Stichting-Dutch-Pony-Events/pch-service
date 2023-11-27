<?php

namespace App\Application\View;

use DateTime;

class ProductView
{
    public function __construct(
        public ?string  $id,
        public string   $name,
        public int      $pretixId,
        public DateTime $createdAt,
        public DateTime $updatedAt,
    ) {
    }
}
