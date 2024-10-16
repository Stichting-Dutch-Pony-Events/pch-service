<?php

namespace App\Application\Request;

use JMS\Serializer\Annotation\Type;

class SetAttendeeRolesRequest
{
    public function __construct(
        /** @var string[] $roles */
        #[Type('array<string>')]
        public array $roles,
    ) {
    }
}