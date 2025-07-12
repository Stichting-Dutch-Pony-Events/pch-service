<?php

namespace App\Application\Service;

use App\Application\Request\ChangeOrderRequest;
use App\Application\Request\QuizAnswerRequest;
use App\DataAccessLayer\Repository\QuizAnswerRepository;
use App\DataAccessLayer\Repository\TeamRepository;
use App\Domain\Entity\QuizAnswer;
use App\Domain\Entity\QuizQuestion;
use App\Domain\Service\QuizAnswerDomainService;
use App\Domain\Service\QuizAnswerTeamWeightDomainService;
use App\Util\Exceptions\Exception\Common\InvalidInputException;
use App\Util\Exceptions\Exception\Entity\EntityNotFoundException;
use Doctrine\ORM\EntityManagerInterface;

readonly class QuizAnswerApplicationService
{
    public function __construct(
        private QuizAnswerDomainService           $quizAnswerDomainService,
        private QuizAnswerTeamWeightDomainService $teamWeightDomainService,
        private QuizAnswerRepository              $quizAnswerRepository,
        private TeamRepository                    $teamRepository,
        private EntityManagerInterface            $entityManager
    ) {
    }

    public function createQuizAnswer(QuizQuestion $quizQuestion, QuizAnswerRequest $quizAnswerRequest): QuizAnswer
    {
        return $this->entityManager->wrapInTransaction(function () use ($quizQuestion, $quizAnswerRequest): QuizAnswer {
            $quizAnswer = $this->quizAnswerDomainService->createAnswer($quizQuestion, $quizAnswerRequest);

            $this->teamWeightDomainService->validateTeamWeights($quizAnswerRequest->teamWeights);
            foreach ($quizAnswerRequest->teamWeights as $teamWeight) {
                $team = $this->teamRepository->find($teamWeight->teamId);
                if (!$team) {
                    throw new EntityNotFoundException("Team with ID {$teamWeight->teamId} not found.");
                }

                $this->teamWeightDomainService->createTeamWeight($quizAnswer, $team, $teamWeight);
            }

            $this->entityManager->persist($quizAnswer);

            return $quizAnswer;
        });
    }

    public function updateQuizAnswer(
        QuizQuestion      $quizQuestion,
        QuizAnswer        $quizAnswer,
        QuizAnswerRequest $quizAnswerRequest
    ): QuizAnswer {
        if ($quizQuestion->getId() !== $quizAnswer->getQuestion()->getId()) {
            throw new InvalidInputException("Quiz question ID does not match the quiz answer's question ID.");
        }

        return $this->entityManager->wrapInTransaction(function () use ($quizAnswer, $quizAnswerRequest): QuizAnswer {
            $quizAnswer = $this->quizAnswerDomainService->updateAnswer($quizAnswer, $quizAnswerRequest);

            $this->teamWeightDomainService->validateTeamWeights($quizAnswerRequest->teamWeights);

            // Remove existing team weights that are not in the request
            $teamWeightIds = array_map(static fn($teamWeight) => $teamWeight->id, $quizAnswerRequest->teamWeights);
            foreach ($quizAnswer->getQuizAnswerTeamWeights() as $teamWeight) {
                if (!in_array($teamWeight->getId(), $teamWeightIds, true)) {
                    $quizAnswer->removeQuizAnswerTeamWeight($teamWeight);
                    $this->entityManager->remove($teamWeight);
                }
            }

            foreach ($quizAnswerRequest->teamWeights as $teamWeight) {
                $team = $this->teamRepository->find($teamWeight->teamId);
                if (!$team) {
                    throw new EntityNotFoundException("Team with ID {$teamWeight->teamId} not found.");
                }

                if ($teamWeight->id === null || $teamWeight->id === '') {
                    // Create new team weight
                    $this->teamWeightDomainService->createTeamWeight($quizAnswer, $team, $teamWeight);
                    continue;
                }
                // Update existing team weight
                $existingTeamWeight = $quizAnswer->getQuizAnswerTeamWeights()->filter(
                    static fn($weight) => $weight->getId() === $teamWeight->id
                )->first();

                if (!$existingTeamWeight) {
                    throw new InvalidInputException("Team weight with ID {$teamWeight->id} not found.");
                }

                $this->teamWeightDomainService->updateTeamWeight($existingTeamWeight, $team, $teamWeight);
            }

            return $quizAnswer;
        });
    }

    public function changeOrder(QuizQuestion $quizQuestion, ChangeOrderRequest $changeOrderRequest): void
    {
        $this->entityManager->wrapInTransaction(function () use ($changeOrderRequest, $quizQuestion): void {
            foreach ($changeOrderRequest->ids as $i => $iValue) {
                $answer = $this->quizAnswerRepository->find($iValue);

                if (!$answer) {
                    throw new EntityNotFoundException("Quiz answer with ID {$iValue} not found.");
                }

                if ($answer->getQuestion()->getId() !== $quizQuestion->getId()) {
                    throw new InvalidInputException("Quiz answer does not belong to the specified quiz question.");
                }

                $answer->setOrder($i + 1);
            }

            $this->entityManager->flush();
        });
    }

    public function deleteQuizAnswer(QuizQuestion $quizQuestion, QuizAnswer $quizAnswer): void
    {
        if ($quizQuestion->getId() !== $quizAnswer->getQuestion()->getId()) {
            throw new InvalidInputException("Quiz question ID does not match the quiz answer's question ID.");
        }
        
        $this->entityManager->wrapInTransaction(function () use ($quizAnswer): void {
            $this->entityManager->remove($quizAnswer);
        });
    }
}