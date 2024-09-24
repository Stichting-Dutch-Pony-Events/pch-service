<?php

declare(strict_types=1);

namespace App\Application\Request;

use App\Domain\Enum\AdminCommandType;

class AdminCommandRequest
{
    public function __construct(
        public AdminCommandType $commandType,
    ) {
    }
}
