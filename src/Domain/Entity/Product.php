<?php

namespace App\Domain\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Gedmo\Timestampable\Traits\Timestampable;
use Symfony\Component\Uid\Uuid;

class Product
{
    use Timestampable;

    private ?Uuid $id = null;

    /** @var Collection<int, CheckInList> */
    private Collection $checkInLists;

    public function __construct(
        private string $name,
        private int    $pretixId,
        private string $defaultRole = 'ROLE_USER',
        /** @var Collection<int, CheckInList> $checkInLists */
        ?Collection    $checkInLists = null,
    ) {
        $this->checkInLists = $checkInLists ?? new ArrayCollection();
    }

    public function getId(): ?string
    {
        return $this->id?->toRfc4122();
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
        if(!$this->checkInLists->contains($checkInList)) {
            $this->checkInLists->add($checkInList);
        }

        return $this;
    }

    public function removeCheckInList(CheckInList $checkInList): self {
        if($this->checkInLists->contains($checkInList)) {
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

    public function getDefaultRole(): string
    {
        return $this->defaultRole;
    }
}
