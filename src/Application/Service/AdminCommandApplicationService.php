<?php

namespace App\Application\Service;

use App\Application\Request\AdminCommandRequest;
use App\DataAccessLayer\Repository\AttendeeRepository;
use App\Domain\Enum\AdminCommandType;
use App\Domain\Service\SettingDomainService;
use App\Domain\Service\TeamDomainService;
use App\Util\Exceptions\Exception\Common\InvalidInputException;
use Doctrine\ORM\EntityManagerInterface;
use Kreait\Firebase\Contract\Messaging;
use Kreait\Firebase\Exception\MessagingException;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Psr\Log\LoggerInterface;

readonly class AdminCommandApplicationService
{
    public function __construct(
        private Messaging              $firebaseMessaging,
        private AttendeeRepository     $attendeeRepository,
        private TeamDomainService      $teamDomainService,
        private EntityManagerInterface $entityManager,
        private LoggerInterface        $logger,
        private SettingDomainService   $settingDomainService
    ) {
    }

    public function executeAdminCommand(AdminCommandRequest $adminCommandRequest): void
    {
        if ($adminCommandRequest->commandType === AdminCommandType::ASSIGN_TEAMS) {
            $this->assignTeams();
        }
    }

    public function assignTeams(): void
    {
        $attendees = $this->attendeeRepository->getAttendeesWithoutTeam();

        $this->entityManager->wrapInTransaction(function () use ($attendees) {
            $this->teamDomainService->assignAttendeesToTeam($attendees);
            $this->settingDomainService->setSetting('auto-assign-teams', '1');
        });

        foreach ($attendees as $attendee) {
            if ($attendee->getTeam() !== null && $attendee->getFireBaseToken() !== null) {
                $message = CloudMessage::withTarget('token', $attendee->getFireBaseToken())
                    ->withNotification(
                        Notification::create(
                            "Team Assigned",
                            "You have been assigned to team " . $attendee->getTeam()->getName()
                        )
                    )
                    ->withData(['refresh-user' => 'true']);

                try {
                    $this->firebaseMessaging->send($message);
                } catch (MessagingException $e) {
                    $this->logger->error($e->getMessage(), ['exception' => $e]);
                }
            }
        }
    }
}