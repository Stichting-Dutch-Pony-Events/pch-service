<?php

namespace App\Application\View;

use App\Application\View\Trait\EntityViewTrait;
use App\Domain\Enum\PrintJobStatusEnum;

class PrintJobView
{
    use EntityViewTrait;

    public function __construct(
        public string             $name,
        public string             $productName,
        public PrintJobStatusEnum $status,
    ) {
    }
}