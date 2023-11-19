<?php

namespace App\Domain\Service;

use App\Domain\Enum\CheckInListTypeEnum;
use App\Models\CheckInList;
use App\DataAccessLayer\Pretix\Views\CheckInList as PretixCheckInList;
use Carbon\Carbon;

class CheckInListDomainService
{
    public function createCheckInList(
        PretixCheckInList $pretixCheckInList,
        CheckInListTypeEnum $checkInListTypeEnum,
        Carbon $start,
        Carbon $end
    ): CheckInList {
        $checkInList = new CheckInList();

        $checkInList->name               = $pretixCheckInList->getName();
        $checkInList->pretix_id          = $pretixCheckInList->getId();
        $checkInList->type               = $checkInListTypeEnum;
        $checkInList->start_time         = $start;
        $checkInList->end_time           = $end;
        $checkInList->pretix_product_ids = $pretixCheckInList->getProductIds();

        $checkInList->save();

        return $checkInList;
    }
}
