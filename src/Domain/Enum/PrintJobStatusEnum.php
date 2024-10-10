<?php

namespace App\Domain\Enum;

enum PrintJobStatusEnum: string
{
    case PENDING = 'PENDING';
    case PRINTING = 'PRINTING';
    case COMPLETED = 'COMPLETED';
    case FAILED = 'FAILED';
}
