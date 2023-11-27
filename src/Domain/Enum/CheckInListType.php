<?php

namespace App\Domain\Enum;

enum CheckInListType: string
{
    case TICKET = 'TICKET';
    case MERCH = 'MERCH';
    case SPECIAL = 'SPECIAL';
}
