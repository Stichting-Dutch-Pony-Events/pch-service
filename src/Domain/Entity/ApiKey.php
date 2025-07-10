<?php

namespace App\Domain\Entity;

use App\Domain\Entity\Trait\HasUuidTrait;
use Gedmo\Timestampable\Traits\Timestampable;

class ApiKey
{
    use Timestampable, HasUuidTrait;

    public function __construct(
        public string   $key,
        public Attendee $attendee,
    ) {
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