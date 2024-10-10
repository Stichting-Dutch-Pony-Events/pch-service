<?php

namespace App\Application\View;

use App\Domain\Enum\PrintJobStatusEnum;
use DateTime;

class PrintJobView
{
    public function __construct(
        public string             $id,
        public string             $name,
        public string             $productName,
        public PrintJobStatusEnum $status,
        public DateTime           $createdAt,
        public DateTime           $updatedAt,
    ) {
    }
}