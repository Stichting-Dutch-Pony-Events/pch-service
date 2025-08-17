<?php

namespace App\Application\Request;

use App\Security\Enum\RoleEnum;
use JMS\Serializer\Annotation\Type;

class SetAttendeeRolesRequest
{
    public function __construct(
        /** @var RoleEnum[] $roles */
        #[Type('array<' . RoleEnum::class . '>')]
        public array $roles,
    ) {
    }
}