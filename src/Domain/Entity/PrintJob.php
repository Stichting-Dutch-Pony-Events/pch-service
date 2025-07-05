<?php

namespace App\Domain\Entity;

use App\Domain\Entity\Trait\HasUuidTrait;
use App\Domain\Enum\PrintJobStatusEnum;
use Gedmo\Timestampable\Traits\Timestampable;

class PrintJob
{
    use Timestampable, HasUuidTrait;

    public function __construct(
        private string             $name,
        private string             $productName,
        private Attendee           $attendee,
        private PrintJobStatusEnum $status,
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getProductName(): string
    {
        return $this->productName;
    }

    public function setProductName(string $productName): self
    {
        $this->productName = $productName;
        return $this;
    }

    public function getAttendee(): Attendee
    {
        return $this->attendee;
    }

    public function setAttendee(Attendee $attendee): self
    {
        $this->attendee = $attendee;
        return $this;
    }

    public function getStatus(): PrintJobStatusEnum
    {
        return $this->status;
    }

    public function setStatus(PrintJobStatusEnum $status): self
    {
        $this->status = $status;
        return $this;
    }
}