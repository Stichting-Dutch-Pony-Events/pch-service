<?php

namespace App\Application\View;

use App\Application\View\Trait\EntityViewTrait;
use App\Security\Enum\RoleEnum;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;

class AttendeeSimpleView
{
    use EntityViewTrait;

    /**
     * @param RoleEnum[]|null $userRoles
     */
    public function __construct(
        public string      $name,
        public ?string     $firstName,
        public ?string     $nickName,
        public ?string     $email,
        public ProductView $product,
        #[OA\Property(
            type: "array",
            items: new OA\Items(ref: new Model(type: RoleEnum::class)),
            nullable: true,
        )]
        public ?array      $userRoles,
    ) {
    }
}