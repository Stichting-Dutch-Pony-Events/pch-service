<?php

namespace App\Models;

use App\Domain\Enum\CheckInListTypeEnum;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Model;

class CheckInList extends Model
{
    protected $casts = [
        'start_time'         => 'datetime',
        'end_time'           => 'datetime',
        'type'               => CheckInListTypeEnum::class,
        'pretix_product_ids' => AsArrayObject::class
    ];
}
