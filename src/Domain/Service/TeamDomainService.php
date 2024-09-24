<?php

declare(strict_types=1);

namespace App\Domain\Service;

use App\DataAccessLayer\Repository\TeamRepository;
use App\Domain\Entity\Attendee;
use App\Domain\Entity\Team;
use Doctrine\Common\Collections\Collection;

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
    public function orderTeamsByLowestAttendeeNumber(array &$teams): void
    {
        usort($teams, static fn (Team $a, Team $b) => $a->getAttendees()->count() <=> $b->getAttendees()->count());
    }
}
