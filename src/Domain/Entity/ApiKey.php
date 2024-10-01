<?php

namespace App\Domain\Entity;

use Gedmo\Timestampable\Traits\Timestampable;
use Symfony\Component\Uid\Uuid;

class ApiKey
{
    use Timestampable;

    private ?Uuid $id = null;

    public function __construct(
        public string   $key,
        public Attendee $attendee,
    ) {
    }

    public function getId(): ?string
    {
        return $this->id?->toRfc4122();
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function getAttendee(): Attendee
    {
        return $this->attendee;
    }
}