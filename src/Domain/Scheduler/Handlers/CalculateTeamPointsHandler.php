<?php

namespace App\Domain\Scheduler\Handlers;

use App\DataAccessLayer\Repository\TeamRepository;
use App\Domain\Scheduler\Messages\CalculateTeamPoints;
use App\Domain\Service\TeamDomainService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class CalculateTeamPointsHandler
{
    public function __construct(
        private TeamDomainService      $teamDomainService,
        private TeamRepository         $teamRepository,
        private EntityManagerInterface $entityManager
    ) {
    }

    public function __invoke(CalculateTeamPoints $message): void
    {
        var_dump('CalculateTeamPointsHandler invoked');
        $teams = $this->teamRepository->findAll();
        foreach ($teams as $team) {
            $this->teamDomainService->calculatePoints($team);
        }

        $this->entityManager->flush();
    }
}