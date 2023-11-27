<?php

namespace App\Domain\Entity;

use App\Domain\Enum\CheckInListType;
use DateTime;
use Gedmo\Timestampable\Traits\Timestampable;
use Symfony\Component\Uid\Uuid;

class CheckInList
{
    use Timestampable;

    private ?Uuid $id;

    public function __construct(
        private string          $name,
        private ?int            $pretixId,
        private DateTime        $startTime,
        private DateTime        $endTime,
        private CheckInListType $type,
        /**
         * @var int[]|null
         */
        private ?array           $pretixProductIds
    ) {
    }

    public function getId(): ?string
    {
        return $this->id?->toRfc4122();
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

    public function getPretixId(): ?int
    {
        return $this->pretixId;
    }

    public function getStartTime(): DateTime
    {
        return $this->startTime;
    }

    public function setStartTime(DateTime $startTime): self
    {
        $this->startTime = $startTime;

        return $this;
    }

    public function getEndTime(): DateTime
    {
        return $this->endTime;
    }

    public function setEndTime(DateTime $endTime): self
    {
        $this->endTime = $endTime;

        return $this;
    }

    public function getType(): CheckInListType
    {
        return $this->type;
    }

    public function setType(CheckInListType $type): self
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return int[]|null
     */
    public function getPretixProductIds(): ?array
    {
        return $this->pretixProductIds;
    }
}
