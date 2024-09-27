<?php

namespace App\Domain\Entity;

use Gedmo\Timestampable\Traits\Timestampable;
use Symfony\Component\Uid\Uuid;

class Achievement
{
    use Timestampable;

    private ?Uuid $id = null;

    public function __construct(
        private string $name,
        private string $description,
        private string $identifier,
        private int    $pointValue = 1
    ) {
    }

    public function getId(): ?string
    {
        return $this->id?->toRfc4122();
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    public function getPointValue(): int
    {
        return $this->pointValue;
    }

    public function setPointValue(int $pointValue): self
    {
        $this->pointValue = $pointValue;

        return $this;
    }
}