<?php

namespace App\Domain\Scheduler;

use App\DataAccessLayer\Repository\TeamRepository;
use App\Domain\Scheduler\Handlers\CalculateTeamPointsHandler;
use App\Domain\Service\TeamDomainService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Scheduler\Attribute\AsSchedule;
use Symfony\Component\Scheduler\RecurringMessage;
use Symfony\Component\Scheduler\Schedule;
use Symfony\Component\Scheduler\ScheduleProviderInterface;

#[AsSchedule("team_points")]
class TeamPointsTaskProvider implements ScheduleProviderInterface
{
    public function __construct()
    {
    }

    private Schedule $schedule;

    public function getSchedule(): Schedule
    {
        return $this->schedule ??= (new Schedule())
            ->with(
                RecurringMessage::every(
                    '1 minute',
                    new CalculateTeamPoints()
                )
            );
    }
}