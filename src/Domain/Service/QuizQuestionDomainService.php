<?php

namespace App\Domain\Service;

use App\DataAccessLayer\Repository\QuizQuestionRepository;
use App\Domain\Entity\QuizQuestion;

readonly class QuizQuestionDomainService
{
    public function __construct(
        private QuizQuestionRepository $quizQuestionRepository,
    ) {
    }

    public function createQuestion(string $question): QuizQuestion
    {
        return new QuizQuestion(
            question: $question,
            order: $this->quizQuestionRepository->getNextOrder(),
        );
    }

    public function updateQuestion(QuizQuestion $quizQuestion, string $question): QuizQuestion
    {
        return $quizQuestion->setQuestion($question);
    }
}