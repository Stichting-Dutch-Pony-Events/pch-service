<?php

namespace App\Application\Request;

use App\Domain\Enum\PrintJobStatusEnum;

class SetPrintJobStatusRequest
{
    public function __construct(
        public PrintJobStatusEnum $status
    ) {
    }
}