<?php

namespace App\Domain\Scheduler\Handlers;

use App\Application\Service\AttendeeApplicationService;
use App\Domain\Scheduler\Messages\CalculateTopScores;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class CalculateTopScoresHandler
{
    public function __construct(
        private AttendeeApplicationService $attendeeApplicationService,
    ) {
    }

    public function __invoke(CalculateTopScores $message)
    {
        $this->attendeeApplicationService->calculateAttendeePositions();
    }
}