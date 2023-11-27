<?php

namespace App\Application\Request;

use App\Domain\Enum\CheckInListType;
use DateTime;
use JMS\Serializer\Annotation\Type;

class CheckInListRequest
{
    public function __construct(
        public string $name,
        public ?int $pretixId,
        public DateTime $startTime,
        public DateTime $endTime,
        public CheckInListType $type,
        /**
         * @var int[]|null
         */
        #[Type('array<int>')]
        public ?array $pretixProductIds,
    )
    {
    }
}
