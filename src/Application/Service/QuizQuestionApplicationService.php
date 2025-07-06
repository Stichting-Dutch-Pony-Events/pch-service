<?php

namespace App\Application\Service;

use App\Application\Request\QuizQuestionRequest;
use App\DataAccessLayer\Repository\TeamRepository;
use App\Domain\Entity\QuizQuestion;
use App\Domain\Service\QuizAnswerDomainService;
use App\Domain\Service\QuizAnswerTeamWeightDomainService;
use App\Domain\Service\QuizQuestionDomainService;
use App\Util\Exceptions\Exception\Common\InvalidInputException;
use Doctrine\ORM\EntityManagerInterface;

readonly class QuizQuestionApplicationService
{
    public function __construct(
        private QuizQuestionDomainService         $quizQuestionDomainService,
        private QuizAnswerDomainService           $quizAnswerDomainService,
        private QuizAnswerTeamWeightDomainService $quizAnswerTeamWeightDomainService,
        private TeamRepository                    $teamRepository,
        private EntityManagerInterface            $entityManager,
    ) {
    }

    public function createQuizQuestion(QuizQuestionRequest $quizQuestionRequest): QuizQuestion
    {
        return $this->entityManager->wrapInTransaction(function () use ($quizQuestionRequest): QuizQuestion {
            $question = $this->quizQuestionDomainService->createQuestion($quizQuestionRequest->question);

            foreach ($quizQuestionRequest->answers as $answer) {
                $quizAnswer = $this->quizAnswerDomainService->createAnswer($answer, $question);

                if ($this->quizAnswerTeamWeightDomainService->validateTeamWeights($answer->teamWeights)) {
                    foreach ($answer->teamWeights as $teamWeight) {
                        $team = $this->teamRepository->find($teamWeight->teamId);
                        if ($team === null) {
                            throw new InvalidInputException("Team not found");
                        }

                        $this->quizAnswerTeamWeightDomainService->createTeamWeight($quizAnswer, $team, $teamWeight);
                    }
                }
            }

            $this->entityManager->persist($question);
            $this->entityManager->flush();

            return $question;
        });
    }
}