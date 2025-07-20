<?php

namespace App\Application\View;

use App\Application\View\Trait\EntityViewTrait;
use JMS\Serializer\Annotation\Type;

class AttendeeSimpleView
{
    use EntityViewTrait;

    public function __construct(
        public string  $name,
        public ?string $firstName,
        public ?string $nickName,
        public ?string $email,

        /** @var string[] $roles */
        #[Type('array<string>')]
        public ?array  $roles,
    ) {
    }
}