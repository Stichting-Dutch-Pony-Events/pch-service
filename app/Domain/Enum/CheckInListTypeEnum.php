<?php

namespace App\Domain\Enum;

enum CheckInListTypeEnum: string
{
    case TICKET = 'TICKET';
    case MERCHANDISE = 'MERCH';
    case SPECIAL_EVENT = 'SPECIAL';
}
