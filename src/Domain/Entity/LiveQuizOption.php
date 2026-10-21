<?php

namespace App\Domain\Entity;

use App\Domain\Entity\Trait\HasUuidTrait;
use Gedmo\Timestampable\Traits\Timestampable;

class LiveQuizOption
{
    use Timestampable, HasUuidTrait;

    public function __construct(
        private LiveQuizQuestion $question,
        private string           $label,
        private int              $order,
    ) {
    }

    public function getQuestion(): LiveQuizQuestion
    {
        return $this->question;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function setLabel(string $label): self
    {
        $this->label = $label;
        return $this;
    }

    public function getOrder(): int
    {
        return $this->order;
    }

    public function setOrder(int $order): self
    {
        $this->order = $order;
        return $this;
    }
}
