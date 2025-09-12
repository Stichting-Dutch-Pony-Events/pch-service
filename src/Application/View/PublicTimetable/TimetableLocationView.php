<?php

namespace App\Application\View\PublicTimetable;

use App\Domain\Enum\TimetableLocationType;
use App\Util\SymfonyUtils\Attribute\MapsMany;
use JMS\Serializer\Annotation\Type;

class TimetableLocationView
{
    /**
     * @param string $id
     * @param string $title
     * @param TimetableLocationType $timetableLocationType
     * @param int $order
     * @param TimetableItemView[] $timetableItems
     */
    public function __construct(
        public string                $id,
        public string                $title,
        public TimetableLocationType $timetableLocationType,
        public int                   $order,
        #[Type('array<' . TimetableItemView::class . '>')]
        #[MapsMany(TimetableItemView::class)]
        public array                 $timetableItems
    ) {
    }
}