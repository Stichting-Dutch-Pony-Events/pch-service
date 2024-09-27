<?php

namespace App\Domain\Entity;

use Gedmo\Timestampable\Traits\Timestampable;
use Symfony\Component\Uid\Uuid;

class Setting
{
    use Timestampable;

    private ?Uuid $id = null;

    public function __construct(
        private string $name,
        private string $value,
    ) {
    }

    public function getId(): ?string {
        return $this?->id->toRfc4122();
    }

    public function getName(): string {
        return $this->name;
    }

    public function getValue(): string {
        return $this->value;
    }

    public function setValue(string $value): self {
        $this->value = $value;

        return $this;
    }
}