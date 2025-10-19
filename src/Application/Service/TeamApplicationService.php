<?php

namespace App\Application\Service;

use App\Application\Request\TeamRequest;
use App\Domain\Entity\Team;
use App\Domain\Service\TeamDomainService;
use Doctrine\ORM\EntityManagerInterface;

readonly class TeamApplicationService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private TeamDomainService      $teamDomainService,
    ) {
    }

    public function createTeam(TeamRequest $teamRequest): Team
    {
        $team = $this->teamDomainService->createTeam(
            name:        $teamRequest->name,
            description: $teamRequest->description,
            identifier:  $teamRequest->identifier,
            colour:      $teamRequest->colour
        );

        $this->entityManager->persist($team);
        $this->entityManager->flush();

        return $team;
    }

    public function updateTeam(Team $team, TeamRequest $teamRequest): Team
    {
        $team = $this->teamDomainService->updateTeam(
            team:        $team,
            name:        $teamRequest->name,
            description: $teamRequest->description,
            identifier:  $teamRequest->identifier,
            colour:      $teamRequest->colour
        );

        $this->entityManager->flush();

        return $team;
    }
}