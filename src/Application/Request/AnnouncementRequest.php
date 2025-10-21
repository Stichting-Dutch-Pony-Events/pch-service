<?php

namespace App\Application\Request;

use App\Domain\Enum\AnnouncementTopicEnum;

class AnnouncementRequest
{
    public function __construct(
        public string                $title,
        public string                $content,
        public AnnouncementTopicEnum $topic
    ) {
    }
}