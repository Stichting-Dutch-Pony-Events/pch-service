<?php

namespace App\Domain\Entity;

use App\Domain\Entity\Trait\HasUuidTrait;
use App\Domain\Enum\AnnouncementTopicEnum;
use Gedmo\Timestampable\Traits\Timestampable;

class Announcement
{
    use HasUuidTrait, Timestampable;

    public function __construct(
        private string                $title,
        private string                $content,
        private AnnouncementTopicEnum $topic = AnnouncementTopicEnum::PCH
    ) {
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getTopic(): AnnouncementTopicEnum
    {
        return $this->topic;
    }
}