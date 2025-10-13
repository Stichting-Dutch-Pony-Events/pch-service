<?php

namespace App\Domain\Entity;

use App\Domain\Entity\Trait\HasUuidTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Gedmo\Timestampable\Traits\Timestampable;

class QuizAnswer
{
    use HasUuidTrait, Timestampable;

    /** @var Collection<array-key, QuizAnswerTeamWeight> $quizAnswerTeamWeights */
    private Collection $quizAnswerTeamWeights;

    /**
     * @param QuizQuestion $question
     * @param string $title
     * @param string $answer
     * @param int $order
     * @param Collection<array-key, QuizAnswerTeamWeight>|null $quizAnswerTeamWeights
     */
    public function __construct(
        private QuizQuestion $question,
        private string       $title,
        private string       $answer,
        private int          $order,
        ?Collection          $quizAnswerTeamWeights = null
    ) {
        $this->quizAnswerTeamWeights = $quizAnswerTeamWeights ?? new ArrayCollection();
        $this->question->addAnswer($this);
    }

    public function getQuestion(): QuizQuestion
    {
        return $this->question;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;
        return $this;
    }

    public function getAnswer(): string
    {
        return $this->answer;
    }

    public function setAnswer(string $answer): self
    {
        $this->answer = $answer;
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

    /**
     * @return Collection<array-key, QuizAnswerTeamWeight>
     */
    public function getQuizAnswerTeamWeights(): Collection
    {
        return $this->quizAnswerTeamWeights;
    }

    public function addQuizAnswerTeamWeight(QuizAnswerTeamWeight $quizAnswerTeamWeight): self
    {
        if (!$this->quizAnswerTeamWeights->contains($quizAnswerTeamWeight)) {
            $this->quizAnswerTeamWeights->add($quizAnswerTeamWeight);
        }

        return $this;
    }

    public function removeQuizAnswerTeamWeight(QuizAnswerTeamWeight $quizAnswerTeamWeight): self
    {
        if ($this->quizAnswerTeamWeights->contains($quizAnswerTeamWeight)) {
            $this->quizAnswerTeamWeights->removeElement($quizAnswerTeamWeight);
        }

        return $this;
    }
}