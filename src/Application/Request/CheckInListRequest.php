<?php

namespace App\Application\Request;

use App\Domain\Enum\CheckInListType;
use DateTime;
use OpenApi\Attributes as OA;

class CheckInListRequest
{
    /**
     * @param string $name
     * @param int|null $pretixId
     * @param DateTime $startTime
     * @param DateTime $endTime
     * @param CheckInListType $type
     * @param int[]|null $pretixProductIds
     */
    public function __construct(
        public string          $name,
        public ?int            $pretixId,
        public DateTime        $startTime,
        public DateTime        $endTime,
        public CheckInListType $type,
        #[OA\Property(
            type: "array",
            items: new OA\Items(type: "integer"),
            nullable: true
        )]
        public ?array          $pretixProductIds,
    ) {
    }
}
