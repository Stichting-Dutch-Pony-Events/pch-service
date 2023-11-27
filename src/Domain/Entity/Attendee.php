<?php

namespace App\Domain\Entity;

use Gedmo\Timestampable\Traits\Timestampable;
use Symfony\Component\Uid\Uuid;

class Attendee
{
    use Timestampable;
    
    private ?Uuid $id = null;

    public function __construct(
        private string  $name,
        private ?string $firstName,
        private ?string $middleName,
        private ?string $familyName,
        private ?string $nickName,
        private ?string $email,
        private string  $orderCode,
        private int     $ticketId,
        private string  $ticketSecret,
        private Product $product
    ) {
    }

    public function getId(): ?string
    {
        return $this->id?->toRfc4122();
    }
}
