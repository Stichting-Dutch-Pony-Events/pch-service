<?php

namespace App\Application\View\PublicTimetable;

use App\Application\View\Trait\EntityViewTrait;
use App\Domain\Enum\TimetableLocationType;
use App\Util\SymfonyUtils\Attribute\MapsMany;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;

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
        #[OA\Property(
            type: "array",
            items: new OA\Items(ref: new Model(type: TimetableItemView::class))
        )]
        #[MapsMany(TimetableItemView::class)]
        public array                 $timetableItems
    ) {
    }
}