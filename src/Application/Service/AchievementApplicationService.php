<?php

namespace App\Application\Service;

use App\Application\Request\AwardAchievementRequest;
use App\Application\Request\UnlockAchievementRequest;
use App\DataAccessLayer\Repository\AchievementRepository;
use App\DataAccessLayer\Repository\AttendeeAchievementRepository;
use App\DataAccessLayer\Repository\AttendeeRepository;
use App\Domain\Entity\Achievement;
use App\Domain\Entity\Attendee;
use App\Domain\Entity\AttendeeAchievement;
use App\Domain\Service\AchievementDomainService;
use App\Domain\Service\AttendeeDomainService;
use App\Util\Exceptions\Exception\Entity\EntityNotFoundException;
use App\Util\Exceptions\Exception\Entity\EntityNotUniqueException;
use Doctrine\ORM\EntityManagerInterface;
use Kreait\Firebase\Contract\Messaging;
use Kreait\Firebase\Exception\MessagingException;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Psr\Log\LoggerInterface;

readonly class AchievementApplicationService
{
    public function __construct(
        private EntityManagerInterface        $entityManager,
        private AttendeeRepository            $attendeeRepository,
        private AchievementRepository         $achievementRepository,
        private AttendeeAchievementRepository $attendeeAchievementRepository,
        private AchievementDomainService      $achievementDomainService,
        private AttendeeDomainService         $attendeeDomainService,
        private Messaging                     $firebaseMessaging,
        private LoggerInterface               $logger,
    ) {
    }

    public function awardAchievement(
        Achievement             $achievement,
        AwardAchievementRequest $awardAchievementRequest
    ): AttendeeAchievement {
        return $this->entityManager->wrapInTransaction(
            function () use ($achievement, $awardAchievementRequest): AttendeeAchievement {
                //Retrieve Attendee
                $attendee = $this->attendeeRepository->loadUserByIdentifier(
                    $awardAchievementRequest->attendeeIdentifier
                );
                if (!$attendee instanceof Attendee) {
                    throw new EntityNotFoundException("Attendee not found");
                }

                foreach ($attendee->getAchievements() as $attendeeAchievement) {
                    if ($attendeeAchievement->getAchievement()->getId() === $achievement->getId()) {
                        throw new EntityNotUniqueException("Attendee already has this achievement");
                    }
                }

                //Create achievement
                $attendeeAchievement = $this->achievementDomainService->awardAchievement($achievement, $attendee);
                $this->entityManager->persist($attendeeAchievement);

                $timeFirstAchievement = $this->attendeeAchievementRepository->getTimeFirstAchievement();
                $attendee = $this->attendeeDomainService->calculatePointsAndTime($attendee, $timeFirstAchievement);

                $this->sendNotification($attendee, $achievement);

                return $attendeeAchievement;
            }
        );
    }

    public function unlockAchievement(
        Attendee                 $attendee,
        UnlockAchievementRequest $unlockAchievementRequest
    ): AttendeeAchievement {
        return $this->entityManager->wrapInTransaction(
            function () use ($attendee, $unlockAchievementRequest): AttendeeAchievement {
                //Retrieve Achievement
                $achievement = $this->achievementRepository->findOneBy(
                    ['unlockCode' => strtolower($unlockAchievementRequest->unlockCode)]
                );
                if (!$achievement instanceof Achievement) {
                    throw new EntityNotFoundException("Achievement not found");
                }

                // Check if attendee doesn't already have achievement
                foreach ($attendee->getAchievements() as $attendeeAchievement) {
                    if ($attendeeAchievement->getAchievementId() === $achievement->getId()) {
                        throw new EntityNotUniqueException("You already unlocked this achievement.");
                    }
                }

                // Create Attendee Achievement
                $attendeeAchievement = $this->achievementDomainService->awardAchievement($achievement, $attendee);
                $this->entityManager->persist($attendeeAchievement);

                $this->sendNotification($attendee, $achievement);

                return $attendeeAchievement;
            }
        );
    }

    private function sendNotification(Attendee $attendee, Achievement $achievement): void
    {
        $token = $attendee->getFireBaseToken();
        if (!empty($token)) {
            $message = CloudMessage::new()
                ->withNotification(
                    Notification::create(
                        "Achievement Unlocked!",
                        "You have unlocked the achievement " . $achievement->getName(),
                    )
                )
                ->withData(['refresh-user' => 'true'])
                ->toToken($token);

            try {
                $this->firebaseMessaging->send($message);
            } catch (MessagingException $e) {
                $this->logger->error($e->getMessage(), ['exception' => $e]);
            }
        }
    }
}