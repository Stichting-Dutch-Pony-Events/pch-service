<?php

namespace App\Application\Service;

use App\Application\Request\CharacterQuizSubmissionRequest;
use App\DataAccessLayer\Repository\QuizAnswerRepository;
use App\Domain\Entity\Attendee;
use App\Domain\Entity\CharacterQuizSubmission;
use App\Domain\Service\CharacterQuizSubmissionAnswerDomainService;
use App\Domain\Service\CharacterQuizSubmissionDomainService;
use App\Util\Exceptions\Exception\Entity\EntityNotFoundException;
use Doctrine\ORM\EntityManagerInterface;

readonly class CharacterQuizSubmissionApplicationService
{
    public function __construct(
        private CharacterQuizSubmissionDomainService       $characterQuizSubmissionDomainService,
        private CharacterQuizSubmissionAnswerDomainService $characterQuizSubmissionAnswerDomainService,
        private QuizAnswerRepository                       $quizAnswerRepository,
        private EntityManagerInterface                     $entityManager,
    ) {
    }

    public function submitCharacterQuiz(
        Attendee                       $attendee,
        CharacterQuizSubmissionRequest $submissionRequest
    ): CharacterQuizSubmission {
        return $this->entityManager->wrapInTransaction(
            function () use ($attendee, $submissionRequest): CharacterQuizSubmission {
                $characterQuizSubmission = $this->characterQuizSubmissionDomainService->createCharacterQuizSubmission(
                    $attendee
                );

                foreach ($submissionRequest->answers as $answer) {
                    $quizAnswer = $this->quizAnswerRepository->find($answer->answerId);
                    if (!$quizAnswer) {
                        throw new EntityNotFoundException('Quiz Answer not found: ' . $answer->answerId);
                    }

                    $this->characterQuizSubmissionAnswerDomainService->createCharacterQuizSubmissionAnswer(
                        $characterQuizSubmission,
                        $quizAnswer
                    );
                }

                $characterQuizSubmission = $this->characterQuizSubmissionDomainService->calculateResults(
                    $characterQuizSubmission
                );

                $this->entityManager->persist($characterQuizSubmission);

                return $characterQuizSubmission;
            }
        );
    }
}