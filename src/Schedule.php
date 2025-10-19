<?php

namespace App;

use App\Domain\Scheduler\Messages\CalculateTeamPoints;
use App\Domain\Scheduler\Messages\CalculateTopScores;
use Symfony\Component\Scheduler\Attribute\AsSchedule;
use Symfony\Component\Scheduler\RecurringMessage;
use Symfony\Component\Scheduler\Schedule as SymfonySchedule;
use Symfony\Component\Scheduler\ScheduleProviderInterface;

#[AsSchedule]
class Schedule implements ScheduleProviderInterface
{
    public function getSchedule(): SymfonySchedule
    {
        return new SymfonySchedule()
            ->with(
                RecurringMessage::every(
                    '5 minutes',
                    new CalculateTeamPoints()
                ),
                RecurringMessage::every(
                    '5 minutes',
                    new CalculateTopScores()
                )
            );
    }
}
