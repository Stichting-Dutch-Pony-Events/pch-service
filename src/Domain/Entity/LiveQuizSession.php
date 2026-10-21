<?php

namespace App\Domain\Entity;

use App\Domain\Entity\Trait\HasUuidTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Gedmo\Timestampable\Traits\Timestampable;

class LiveQuizSession
{
    use Timestampable, HasUuidTrait;

    /** @var Collection<array-key, LiveQuizQuestion> */
    private Collection $questions;

    /** @var Collection<array-key, LiveQuizParticipant> */
    private Collection $participants;

    /**
     * @param Collection<array-key, LiveQuizQuestion>|null $questions
     * @param Collection<array-key, LiveQuizParticipant>|null $participants
     */
    public function __construct(
        private string    $name,
        private string    $password,
        private bool      $isActive = true,
        private ?Attendee $createdBy = null,
        ?Collection       $questions = null,
        ?Collection       $participants = null,
    ) {
        $this->questions = $questions ?? new ArrayCollection();
        $this->participants = $participants ?? new ArrayCollection();
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

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;
        return $this;
    }

    public static function isHashedPassword(string $password): bool
    {
        return password_get_info($password)['algo'] !== null;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function getIsActive(): bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;
        return $this;
    }

    public function getCreatedBy(): ?Attendee
    {
        return $this->createdBy;
    }

    public function setCreatedBy(?Attendee $createdBy): self
    {
        $this->createdBy = $createdBy;
        return $this;
    }

    /** @return Collection<array-key, LiveQuizQuestion> */
    public function getQuestions(): Collection
    {
        return $this->questions;
    }

    /** @return Collection<array-key, LiveQuizParticipant> */
    public function getParticipants(): Collection
    {
        return $this->participants;
    }
}
