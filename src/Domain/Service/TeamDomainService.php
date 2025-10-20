<?php

declare(strict_types=1);

namespace App\Domain\Service;

use App\DataAccessLayer\Repository\TeamRepository;
use App\Domain\Entity\Attendee;
use App\Domain\Entity\CharacterQuizSubmission;
use App\Domain\Entity\CharacterQuizSubmissionTeamResult;
use App\Domain\Entity\Team;
use App\Util\Exceptions\Exception\Common\InvalidInputException;
use App\Util\Exceptions\Exception\Entity\EntityNotPersistedException;

readonly class TeamDomainService
{
    public function __construct(
        private TeamRepository $teamRepository,
    ) {
    }

    /**
     * @param  Attendee[]  $attendees
     * @return void
     * @throws EntityNotPersistedException
     */
    public function assignAttendeesToTeam(array $attendees): void
    {
        $teams = $this->teamRepository->findAll();

        /** @var Attendee[] $attendeesWithQuizSubmission */
        $attendeesWithQuizSubmission = [];
        /** @var Attendee[] $attendeesWithoutQuizSubmission */
        $attendeesWithoutQuizSubmission = [];

        foreach ($attendees as $attendee) {
            if ($attendee->getCharacterQuizSubmissions()->isEmpty()) {
                $attendeesWithoutQuizSubmission[] = $attendee;
            } else {
                $attendeesWithQuizSubmission[] = $attendee;
            }
        }

        /** @var array<string, Attendee[]> $teamAttendees */
        $teamAttendees = [];
        foreach ($teams as $team) {
            $teamAttendees[$team->getId()] = [];
        }

        foreach ($attendeesWithQuizSubmission as $attendee) {
            $highestTeam = $this->getHighestScoringTeamForAttendee($attendee);

            if ($highestTeam === null) {
                continue;
            }

            $teamAttendees[$highestTeam->getId()][] = $highestTeam;
        }

        while (count($attendeesWithoutQuizSubmission) > 0) {
            $teamId                   = $this->getTeamIdWithLowestCount($teamAttendees);
            $teamAttendees[$teamId][] = array_shift($attendeesWithoutQuizSubmission);
        }

        do {
            $teamIdHighestCount = $this->getTeamIdWithoutHighestCount($teamAttendees);
            $teamIdLowestCount  = $this->getTeamIdWithLowestCount($teamAttendees);

            $difference = abs(count($teamAttendees[$teamIdHighestCount]) - count($teamAttendees[$teamIdLowestCount]));

            if ($difference > 1) {
                $teamAttendees[$teamIdLowestCount][] = array_pop($teamAttendees[$teamIdHighestCount]);
            }
        } while ($difference > 2);

        $teamIndexed = [];
        foreach ($teams as $team) {
            $teamIndexed[$team->getId()] = $team;
        }

        foreach ($teamAttendees as $teamId => $attendees) {
            $team = $teamIndexed[$teamId];
            foreach ($attendees as $attendee) {
                $attendee->setTeam($team);
            }
        }
    }

    public function getHighestScoringTeamForAttendee(Attendee $attendee): ?CharacterQuizSubmissionTeamResult
    {
        $characterQuizSubmission = $attendee->getCharacterQuizSubmissions()->first();

        if (!$characterQuizSubmission instanceof CharacterQuizSubmission) {
            return null;
        }

        $teamResults = $characterQuizSubmission->getTeamResults()->toArray();

        usort($teamResults, function ($a, $b) {
            return $a->getScore() <=> $b->getScore();
        });

        return count($teamResults) === 1 ? $teamResults[0] : null;
    }

    /**
     * @param  array<string, Attendee[]>  $teams
     * @return string|null
     */
    private function getTeamIdWithLowestCount(array $teams): ?string
    {
        $lowestTeamId    = null;
        $lowestTeamCount = PHP_INT_MAX;
        foreach ($teams as $key => $value) {
            if (count($value) < $lowestTeamCount) {
                $lowestTeamId    = $key;
                $lowestTeamCount = count($value);
            }
        }

        return $lowestTeamId;
    }

    /**
     * @param  array<string, Attendee[]>  $teams
     * @return string|null
     */
    private function getTeamIdWithoutHighestCount(array $teams): ?string
    {
        $highestTeamId    = null;
        $highestTeamCount = 0;

        foreach ($teams as $key => $value) {
            if (count($value) > $highestTeamCount) {
                $highestTeamId    = $key;
                $highestTeamCount = count($value);
            }
        }

        return $highestTeamId;
    }

    /**
     * @param  Team[]  $teams
     */
    private function orderTeamsByLowestAttendeeNumber(array &$teams): void
    {
        usort($teams, static fn(Team $a, Team $b) => $a->getAttendees()->count() <=> $b->getAttendees()->count());
    }

    public function createTeam(string $name, string $description, string $identifier, string $colour): Team
    {
        if (!preg_match('/^#[0-9a-fA-F]{6}$/', $colour)) {
            throw new InvalidInputException("Colour must be a valid hex code.");
        }

        return new Team($name, $description, $identifier, 0, $colour);
    }

    public function updateTeam(Team $team, string $name, string $description, string $identifier, string $colour): Team
    {
        if (!preg_match('/^#[0-9a-fA-F]{6}$/', $colour)) {
            throw new InvalidInputException("Colour must be a valid hex code.");
        }

        return $team
            ->setName($name)
            ->setDescription($description)
            ->setIdentifier($identifier)
            ->setColour($colour);
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
