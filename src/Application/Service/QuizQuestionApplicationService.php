<?php

namespace App\Application\Service;

use App\Application\Request\ChangeOrderRequest;
use App\Application\Request\QuizQuestionRequest;
use App\DataAccessLayer\Repository\QuizQuestionRepository;
use App\Domain\Entity\QuizQuestion;
use App\Domain\Service\QuizQuestionDomainService;
use Doctrine\ORM\EntityManagerInterface;

readonly class QuizQuestionApplicationService
{
    public function __construct(
        private QuizQuestionDomainService $quizQuestionDomainService,
        private QuizQuestionRepository    $quizQuestionRepository,
        private EntityManagerInterface    $entityManager,
    ) {
    }

    public function createQuizQuestion(QuizQuestionRequest $quizQuestionRequest): QuizQuestion
    {
        return $this->entityManager->wrapInTransaction(function () use ($quizQuestionRequest): QuizQuestion {
            $question = $this->quizQuestionDomainService->createQuestion(
                title: $quizQuestionRequest->title,
                question: $quizQuestionRequest->question
            );

            $this->entityManager->persist($question);
            $this->entityManager->flush();

            return $question;
        });
    }

    public function updateQuizQuestion(
        QuizQuestion        $quizQuestion,
        QuizQuestionRequest $quizQuestionRequest
    ): QuizQuestion {
        return $this->entityManager->wrapInTransaction(function () use (
            $quizQuestion,
            $quizQuestionRequest
        ): QuizQuestion {
            $question = $this->quizQuestionDomainService->updateQuestion(
                quizQuestion: $quizQuestion,
                title: $quizQuestionRequest->title,
                question: $quizQuestionRequest->question
            );

            $this->entityManager->flush();

            return $question;
        });
    }

    public function changeOrder(ChangeOrderRequest $changeOrderRequest): void
    {
        $this->entityManager->wrapInTransaction(function () use ($changeOrderRequest): void {
            foreach ($changeOrderRequest->ids as $index => $indexValue) {
                $question = $this->quizQuestionRepository->find($indexValue);
                if (!$question) {
                    continue;
                }

                $question->setOrder($index + 1);
            }

            $this->entityManager->flush();
        });
    }

    public function deleteQuestion(QuizQuestion $question): void
    {
        $this->entityManager->wrapInTransaction(function () use ($question): void {
            $this->entityManager->remove($question);
        });
    }
}