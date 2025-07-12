<?php

namespace App\Domain\Service;

use App\Application\Request\QuizAnswerRequest;
use App\DataAccessLayer\Repository\QuizAnswerRepository;
use App\Domain\Entity\QuizAnswer;
use App\Domain\Entity\QuizQuestion;

readonly class QuizAnswerDomainService
{
    public function __construct(
        private QuizAnswerRepository $quizAnswerRepository
    ) {
    }

    public function createAnswer(QuizQuestion $quizQuestion, QuizAnswerRequest $quizAnswerRequest): QuizAnswer
    {
        return new QuizAnswer(
            question: $quizQuestion,
            title: $quizAnswerRequest->title,
            answer: $quizAnswerRequest->answer,
            order: $this->quizAnswerRepository->getNextOrder($quizQuestion)
        );
    }

    public function updateAnswer(QuizAnswer $quizAnswer, QuizAnswerRequest $quizAnswerRequest): QuizAnswer
    {
        return $quizAnswer
            ->setTitle($quizAnswerRequest->title)
            ->setAnswer($quizAnswerRequest->answer);
    }
}