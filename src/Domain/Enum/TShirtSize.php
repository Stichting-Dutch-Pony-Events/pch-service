<?php

namespace App\Domain\Enum;

enum TShirtSize: string
{
    case S = 's';
    case M = 'm';
    case L = 'l';
    case XL = 'xl';
    case XXL = 'xxl';
}