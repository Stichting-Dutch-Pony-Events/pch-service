<?php

namespace App\Application\View\PublicTimetable;

use App\Application\View\Trait\EntityViewTrait;
use App\Domain\Enum\TimetableLocationType;
use App\Util\SymfonyUtils\Attribute\MapsMany;
use JMS\Serializer\Annotation\Type;

class TimetableLocationView
{
    use EntityViewTrait;

    /**
     * @param string $title
     * @param TimetableLocationType $timetableLocationType
     * @param int $order
     * @param TimetableItemView[] $timetableItems
     */
    public function __construct(
        public string                $title,
        public TimetableLocationType $timetableLocationType,
        public int                   $order,
        #[Type('array<' . TimetableItemView::class . '>')]
        #[MapsMany(TimetableItemView::class)]
        public array                 $timetableItems
    ) {
    }
}