<?php

namespace App\Application\Request;

use App\Security\Enum\RoleEnum;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;

class SetAttendeeRolesRequest
{
    /**
     * @param RoleEnum[] $roles
     */
    public function __construct(
        #[OA\Property(
            type: "array",
            items: new OA\Items(ref: new Model(type: RoleEnum::class))
        )]
        public array $roles,
    ) {
    }
}