<?php

namespace App\Application\View;

use App\Application\View\Trait\EntityViewTrait;
use App\Domain\Enum\AnnouncementTopicEnum;

class AnnouncementView
{
    use EntityViewTrait;

    public function __construct(
        public string                $title,
        public string                $content,
        public AnnouncementTopicEnum $topic
    ) {
    }
}