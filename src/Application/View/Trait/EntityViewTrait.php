<?php

namespace App\Application\View\Trait;

use DateTime;

trait EntityViewTrait
{
    public string $id;
    public DateTime $createdAt;
    public DateTime $updatedAt;
}