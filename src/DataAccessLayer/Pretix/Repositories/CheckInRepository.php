<?php

namespace App\DataAccessLayer\Pretix\Repositories;

use App\DataAccessLayer\Pretix\Request\CheckInRequest;
use App\DataAccessLayer\Pretix\Views\CheckIn;

class CheckInRepository extends PretixBaseRepository
{
    public function checkIn(CheckInRequest $checkInRequest): CheckIn {
        $response = $this->pretixApiClient->post('checkinrpc/redeem', $checkInRequest, false);
        return new CheckIn($response);
    }
}
