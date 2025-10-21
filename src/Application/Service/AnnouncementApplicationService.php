<?php

namespace App\Application\Service;

use App\Application\Request\AnnouncementRequest;
use App\Domain\Entity\Announcement;
use App\Domain\Service\AnnouncementDomainService;
use Doctrine\ORM\EntityManagerInterface;
use Kreait\Firebase\Contract\Messaging;
use Kreait\Firebase\Exception\FirebaseException;
use Kreait\Firebase\Exception\MessagingException;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Psr\Log\LoggerInterface;

readonly class AnnouncementApplicationService
{
    public function __construct(
        private EntityManagerInterface    $entityManager,
        private Messaging                 $firebaseMessaging,
        private LoggerInterface           $logger,
        private AnnouncementDomainService $announcementDomainService
    ) {
    }

    public function createAnnouncement(AnnouncementRequest $request): Announcement
    {
        /** @var Announcement $announcement */
        $announcement = $this->entityManager->wrapInTransaction(function () use ($request): Announcement {
            $announcement = $this->announcementDomainService->createAnnouncement($request);

            $this->entityManager->persist($announcement);

            return $announcement;
        });

        $message = CloudMessage::new()
            ->withNotification(
                Notification::create(
                    $announcement->getTitle(),
                    $announcement->getContent(),
                )
            )
            ->toTopic($announcement->getTopic()->value);

        try {
            $this->firebaseMessaging->send($message);
        } catch (MessagingException|FirebaseException $e) {
            $this->logger->error($e->getMessage(), ['exception' => $e]);
        }

        return $announcement;
    }
}