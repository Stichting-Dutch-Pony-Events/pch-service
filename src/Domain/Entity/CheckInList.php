<?php

namespace App\Domain\Entity;

use App\Domain\Entity\Trait\HasUuidTrait;
use App\Domain\Enum\CheckInListType;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Gedmo\Timestampable\Traits\Timestampable;

class CheckInList
{
    use Timestampable, HasUuidTrait;

    /** @var Collection<int, Product> $products */
    private Collection $products;

    public function __construct(
        private string          $name,
        private ?int            $pretixId,
        private DateTime        $startTime,
        private DateTime        $endTime,
        private CheckInListType $type,
        /** @var int[]|null $pretixProductIds */
        private ?array          $pretixProductIds,
        /** @var Collection<int, Product> $products */
        ?Collection             $products = null,
    ) {
        $this->products = $products ?? new ArrayCollection();
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

    /** @return Collection<int, Product> */
    public function getProducts(): Collection
    {
        return $this->products;
    }

    public function addProduct(Product $product): self
    {
        if (!$this->products->contains($product)) {
            $this->products->add($product);
        }

        return $this;
    }

    public function removeProduct(Product $product): self
    {
        if ($this->products->contains($product)) {
            $this->products->removeElement($product);
        }

        return $this;
    }
}
