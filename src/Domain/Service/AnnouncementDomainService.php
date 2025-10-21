<?php

namespace App\Domain\Service;

use App\Application\Request\AnnouncementRequest;
use App\Domain\Entity\Announcement;

readonly class AnnouncementDomainService
{
    public function createAnnouncement(AnnouncementRequest $request): Announcement
    {
        return new Announcement(
            title: $request->title,
            content: $request->content,
            topic: $request->topic,
        );
    }
}