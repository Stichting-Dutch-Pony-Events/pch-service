<?php

namespace App\Domain\Entity;

use App\Domain\Entity\Trait\HasUuidTrait;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Gedmo\Timestampable\Traits\Timestampable;

class LiveQuizQuestion
{
    use Timestampable, HasUuidTrait;

    /** @var Collection<array-key, LiveQuizOption> */
    private Collection $options;

    /** @var Collection<array-key, LiveQuizResponse> */
    private Collection $responses;

    /**
     * @param Collection<array-key, LiveQuizOption>|null $options
     * @param Collection<array-key, LiveQuizResponse>|null $responses
     */
    public function __construct(
        private LiveQuizSession $session,
        private ?string         $label = null,
        private int             $durationSeconds = 15,
        private ?DateTime       $openedAt = null,
        ?Collection             $options = null,
        ?Collection             $responses = null,
    ) {
        $this->options = $options ?? new ArrayCollection();
        $this->responses = $responses ?? new ArrayCollection();
    }

    public function getSession(): LiveQuizSession
    {
        return $this->session;
    }

    public function getSessionId(): string
    {
        return $this->session->getId();
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(?string $label): self
    {
        $this->label = $label;
        return $this;
    }

    public function getDurationSeconds(): int
    {
        return $this->durationSeconds;
    }

    public function setDurationSeconds(int $durationSeconds): self
    {
        $this->durationSeconds = $durationSeconds;
        return $this;
    }

    public function getOpenedAt(): ?DateTime
    {
        return $this->openedAt;
    }

    public function setOpenedAt(?DateTime $openedAt): self
    {
        $this->openedAt = $openedAt;
        return $this;
    }

    /** @return Collection<array-key, LiveQuizOption> */
    public function getOptions(): Collection
    {
        return $this->options;
    }

    /** @return Collection<array-key, LiveQuizResponse> */
    public function getResponses(): Collection
    {
        return $this->responses;
    }

    public function isOpen(): bool
    {
        if ($this->openedAt === null) {
            return false;
        }
        $closeAt = (clone $this->openedAt)->modify("+{$this->durationSeconds} seconds");
        return new DateTime() < $closeAt;
    }

    public function isClosed(): bool
    {
        return $this->openedAt !== null && !$this->isOpen();
    }

    public function getIsOpen(): bool
    {
        return $this->isOpen();
    }

    public function getIsClosed(): bool
    {
        return $this->isClosed();
    }
}
