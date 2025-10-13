<?php

namespace App\Domain\Entity;

use App\Domain\Entity\Trait\HasUuidTrait;
use App\Security\Enum\RoleEnum;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Gedmo\Timestampable\Traits\Timestampable;

class Product
{
    use Timestampable, HasUuidTrait;

    /** @var Collection<array-key, CheckInList> */
    private Collection $checkInLists;

    /**
     * @param string $name
     * @param int $pretixId
     * @param RoleEnum $defaultRole
     * @param Collection<array-key, CheckInList>|null $checkInLists
     */
    public function __construct(
        private string   $name,
        private int      $pretixId,
        private RoleEnum $defaultRole = RoleEnum::USER,
        ?Collection      $checkInLists = null,
    ) {
        $this->checkInLists = $checkInLists ?? new ArrayCollection();
    }

    /**
     * @return Collection<int, CheckInList>
     */
    public function getCheckInLists(): Collection
    {
        return $this->checkInLists;
    }

    public function addCheckInList(CheckInList $checkInList): self
    {
        if (!$this->checkInLists->contains($checkInList)) {
            $this->checkInLists->add($checkInList);
        }

        return $this;
    }

    public function removeCheckInList(CheckInList $checkInList): self
    {
        if ($this->checkInLists->contains($checkInList)) {
            $this->checkInLists->removeElement($checkInList);
        }

        return $this;
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

    public function getPretixId(): int
    {
        return $this->pretixId;
    }

    public function getDefaultRole(): RoleEnum
    {
        return $this->defaultRole;
    }
}
