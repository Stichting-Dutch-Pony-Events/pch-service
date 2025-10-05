<?php

namespace App\Domain\Service;

use App\Domain\Entity\CharacterQuizSubmission;
use App\Domain\Entity\CharacterQuizSubmissionAnswer;
use App\Domain\Entity\QuizAnswer;

readonly class CharacterQuizSubmissionAnswerDomainService
{
    public function createCharacterQuizSubmissionAnswer(
        CharacterQuizSubmission $characterQuizSubmission,
        QuizAnswer              $quizAnswer
    ): CharacterQuizSubmissionAnswer {
        $quizQuestion = $quizAnswer->getQuestion();

        if ($characterQuizSubmission->getAnswers()->exists(
            function ($key, CharacterQuizSubmissionAnswer $existingAnswer) use ($quizQuestion) {
                return $existingAnswer->getQuestion() === $quizQuestion;
            }
        )) {
            throw new \InvalidArgumentException('An answer for this question already exists in the submission.');
        }

        return new CharacterQuizSubmissionAnswer(
            $quizQuestion,
            $quizAnswer,
            $characterQuizSubmission
        );
    }
}