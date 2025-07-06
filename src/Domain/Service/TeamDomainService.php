<?php

declare(strict_types=1);

namespace App\Domain\Service;

use App\DataAccessLayer\Repository\TeamRepository;
use App\Domain\Entity\Attendee;
use App\Domain\Entity\Team;

readonly class TeamDomainService
{
    public function __construct(private TeamRepository $teamRepository)
    {
    }

    /**
     * @param Attendee[] $attendees
     * @return void
     */
    public function assignAttendeesToTeam(array $attendees): void
    {
        shuffle($attendees);

        foreach ($attendees as $attendee) {
            $attendee->setTeam(null);
        }

        $teams = $this->teamRepository->findAll();

        foreach ($attendees as $attendee) {
            $this->orderTeamsByLowestAttendeeNumber($teams);

            $attendee->setTeam($teams[0]);
        }
    }

    /**
     * @param Team[] $teams
     */
    private function orderTeamsByLowestAttendeeNumber(array &$teams): void
    {
        usort($teams, static fn(Team $a, Team $b) => $a->getAttendees()->count() <=> $b->getAttendees()->count());
    }

    public function createTeam(string $name, string $description, string $identifier): Team
    {
        return new Team($name, $description, $identifier);
    }

    public function updateTeam(Team $team, string $name, string $description, string $identifier): Team
    {
        return $team
            ->setName($name)
            ->setDescription($description)
            ->setIdentifier($identifier);
    }

    public function calculatePoints(Team $team): Team
    {
        $points = 0;

        foreach ($team->getAttendees() as $attendee) {
            foreach ($attendee->getAchievements() as $achievement) {
                $points += $achievement->getAchievement()->getPointValue();
            }
        }

        $team->setPoints($points);

        return $team;
    }
}
