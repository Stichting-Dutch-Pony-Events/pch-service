<?php

namespace App\Domain\Entity;

use App\Domain\Entity\Trait\HasUuidTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Gedmo\Timestampable\Traits\Timestampable;

class QuizQuestion
{
    use Timestampable, HasUuidTrait;

    /** @var Collection<array-key, QuizAnswer> $answers */
    private Collection $answers;

    public function __construct(
        private string $question,
        private int    $order,
        ?Collection    $answers = null
    ) {
        $this->answers = $answers ?? new ArrayCollection();
    }

    public function getQuestion(): string
    {
        return $this->question;
    }

    public function setQuestion(string $question): QuizQuestion
    {
        $this->question = $question;
        return $this;
    }

    public function getOrder(): int
    {
        return $this->order;
    }

    public function setOrder(int $order): QuizQuestion
    {
        $this->order = $order;
        return $this;
    }

    /**
     * @return Collection<array-key, QuizAnswer>
     */
    public function getAnswers(): Collection
    {
        return $this->answers;
    }

    public function addAnswer(QuizAnswer $answer): self
    {
        if (!$this->answers->contains($answer)) {
            $this->answers->add($answer);
        }

        return $this;
    }

    public function removeAnswer(QuizAnswer $answer): self
    {
        if ($this->answers->contains($answer)) {
            $this->answers->removeElement($answer);
        }

        return $this;
    }
}