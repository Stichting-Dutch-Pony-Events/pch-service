<?php

declare(strict_types=1);

namespace App\Domain\Enum;

enum AdminCommandType: string
{
    case ASSIGN_TEAMS = 'assign_teams';
}
