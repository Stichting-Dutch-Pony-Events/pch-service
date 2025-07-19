<?php

namespace App\Application\View;

use App\Application\View\Trait\EntityViewTrait;
use App\Domain\Enum\TimetableLocationType;
use App\Util\SymfonyUtils\Attribute\MapsMany;
use JMS\Serializer\Annotation\Type;

class TimetableLocationView
{
    use EntityViewTrait;

    public function __construct(
        public string                $title,
        public TimetableLocationType $timetableLocationType,
        public int                   $order,

        /** @var TimetableDayView[] $timetableDays */
        #[Type('array<' . TimetableDayView::class . '>')]
        #[MapsMany(TimetableDayView::class)]
        public array                 $timetableDays = [],
    ) {
    }
}