<?php

namespace App\Domain\Entity;

use App\Domain\Enum\TShirtSize;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Gedmo\Timestampable\Traits\Timestampable;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Uid\Uuid;

class Attendee implements UserInterface, PasswordAuthenticatedUserInterface
{
    use Timestampable;

    private ?Uuid $id = null;

    /** @var Collection<int, CheckIn> $checkIns */
    private Collection $checkIns;

    /** @var Collection<int, AttendeeAchievement> $achievements */
    private Collection $achievements;

    public function __construct(
        private string      $name,
        private ?string     $firstName,
        private ?string     $middleName,
        private ?string     $familyName,
        private ?string     $nickName,
        private ?string     $email,
        private string      $orderCode,
        private int         $ticketId,
        private string      $ticketSecret,
        private Product     $product,
        private ?Team       $team = null,
        private ?TShirtSize $tShirtSize = null,
        private ?string     $nfcTagId = null,
        private ?string     $miniIdentifier = null,
        private ?string     $password = null,
        private ?string     $fireBaseToken = null,
        private ?string     $badgeFile = null,
        private ?array      $roles = ['ROLE_USER'],
        /** @var Collection<int, CheckIn> $checkIns */
        ?Collection         $checkIns = null,

        /** @var Collection<int, AttendeeAchievement> $achievements */
        ?Collection         $achievements = null,
    ) {
        $this->checkIns = $checkIns ?? new ArrayCollection();
        $this->achievements = $achievements ?? new ArrayCollection();
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

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(?string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getMiddleName(): ?string
    {
        return $this->middleName;
    }

    public function setMiddleName(?string $middleName): self
    {
        $this->middleName = $middleName;

        return $this;
    }

    public function getFamilyName(): ?string
    {
        return $this->familyName;
    }

    public function setFamilyName(?string $familyName): self
    {
        $this->familyName = $familyName;

        return $this;
    }

    public function getNickName(): ?string
    {
        return $this->nickName;
    }

    public function setNickName(?string $nickName): self
    {
        $this->nickName = $nickName;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getOrderCode(): string
    {
        return $this->orderCode;
    }

    public function getTicketId(): int
    {
        return $this->ticketId;
    }

    public function getTicketSecret(): string
    {
        return $this->ticketSecret;
    }

    public function getProduct(): Product
    {
        return $this->product;
    }

    public function getTeam(): ?Team
    {
        return $this->team;
    }

    public function setTeam(?Team $team): self
    {
        $this->team?->removeAttendee($this);
        $team?->addAttendee($this);

        $this->team = $team;

        return $this;
    }

    /**
     * @return Collection<int, CheckIn>
     */
    public function getCheckIns(): Collection
    {
        return $this->checkIns;
    }

    public function getNfcTagId(): ?string
    {
        return $this->nfcTagId;
    }

    public function setNfcTagId(?string $nfcTagId): self
    {
        $this->nfcTagId = $nfcTagId;

        return $this;
    }

    public function getMiniIdentifier(): ?string
    {
        return $this->miniIdentifier;
    }

    public function getTShirtSize(): ?TShirtSize
    {
        return $this->tShirtSize;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(?string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getFireBaseToken(): ?string
    {
        return $this->fireBaseToken;
    }

    public function setFireBaseToken(?string $fireBaseToken): self
    {
        $this->fireBaseToken = $fireBaseToken;

        return $this;
    }

    public function getBadgeFile(): ?string {
        return $this->badgeFile;
    }

    public function setBadgeFile(string $badgeFile): self
    {
        $this->badgeFile = $badgeFile;

        return $this;
    }

    public function setRoles(array $roles): self
    {
        if (!in_array('ROLE_USER', $roles, true)) {
            $roles[] = 'ROLE_USER';
        }

        $this->roles = $roles;
        return $this;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function eraseCredentials(): void
    {
    }

    public function getUserIdentifier(): string
    {
        return $this->getMiniIdentifier() ?? $this->getId();
    }

    /**
     * @return Collection<int, AttendeeAchievement>
     */
    public function getAchievements(): Collection
    {
        return $this->achievements;
    }

    public function addAchievement(AttendeeAchievement $achievement): self
    {
        if (!$this->achievements->contains($achievement)) {
            $this->achievements->add($achievement);
        }

        return $this;
    }

    public function removeAchievement(AttendeeAchievement $achievement): self
    {
        if ($this->achievements->contains($achievement)) {
            $this->achievements->removeElement($achievement);
        }

        return $this;
    }
}
