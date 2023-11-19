<?php

namespace App\Application\Service;

use App\DataAccessLayer\Pretix\Views\CheckInList as PretixCheckInList;
use App\Domain\Enum\CheckInListTypeEnum;
use App\Domain\Service\CheckInListDomainService;
use App\Models\CheckInList;
use Carbon\Carbon;

class CheckInListApplicationService
{
    public function __construct(
        private readonly CheckInListDomainService $checkInListDomainService,
    ) {
    }

    public function createCheckInList(
        PretixCheckInList $pretixCheckInList,
        CheckInListTypeEnum $checkInListTypeEnum,
        Carbon $start,
        Carbon $end
    ): CheckInList {
        return $this->checkInListDomainService->createCheckInList($pretixCheckInList, $checkInListTypeEnum, $start,
            $end);
    }

    public function getActiveCheckInList(CheckInListTypeEnum $type): ?CheckInList {
        return CheckInList::where('start_time', '>=', Carbon::now())->where('end_time', '<=', Carbon::now())->where('type', $type->value)->orderBy('start_time', 'ASC')->first();
    }
}
