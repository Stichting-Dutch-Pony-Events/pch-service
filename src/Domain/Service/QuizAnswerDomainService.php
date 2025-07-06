<?php

namespace App\Domain\Service;

use App\Application\Request\QuizAnswerRequest;
use App\Domain\Entity\QuizAnswer;
use App\Domain\Entity\QuizQuestion;

readonly class QuizAnswerDomainService
{
    public function createAnswer(QuizAnswerRequest $quizAnswerRequest, QuizQuestion $quizQuestion): QuizAnswer
    {
        return new QuizAnswer(
            question: $quizQuestion,
            answer: $quizAnswerRequest->answer,
            order: $quizAnswerRequest->order
        );
    }
}