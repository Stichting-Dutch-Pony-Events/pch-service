<?php

namespace App\Application\View;

use App\Application\View\Trait\EntityViewTrait;
use App\Security\Enum\RoleEnum;
use JMS\Serializer\Annotation\Type;

class AttendeeSimpleView
{
    use EntityViewTrait;

    public function __construct(
        public string      $name,
        public ?string     $firstName,
        public ?string     $nickName,
        public ?string     $email,
        public ProductView $product,

        /** @var RoleEnum[] $userRoles */
        #[Type('array<' . RoleEnum::class . '>')]
        public ?array      $userRoles,
    ) {
    }
}