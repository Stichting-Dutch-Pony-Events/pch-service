<?php

namespace App\Domain\Entity;

use App\Domain\Entity\Contract\EnumUserInterface;
use App\Domain\Entity\Trait\HasUuidTrait;
use App\Domain\Enum\TShirtSize;
use App\Security\Enum\RoleEnum;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Gedmo\Timestampable\Traits\Timestampable;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

class Attendee implements EnumUserInterface, PasswordAuthenticatedUserInterface
{
    use Timestampable, HasUuidTrait;

    /** @var Collection<array-key, CheckIn> $checkIns */
    private Collection $checkIns;

    /** @var Collection<array-key, AttendeeAchievement> $achievements */
    private Collection $achievements;

    /** @var Collection<array-key, PrintJob> $printJobs */
    private Collection $printJobs;

    /** @var Collection<array-key, TimetableItem> $timetableItems */
    private Collection $timetableItems;

    /** @var Collection<array-key, CharacterQuizSubmission> $characterQuizSubmissions */
    private Collection $characterQuizSubmissions;

    /**
     * @param  string  $name
     * @param  string|null  $firstName
     * @param  string|null  $middleName
     * @param  string|null  $familyName
     * @param  string|null  $nickName
     * @param  string|null  $email
     * @param  string  $orderCode
     * @param  int  $ticketId
     * @param  string  $ticketSecret
     * @param  Product  $product
     * @param  Team|null  $team
     * @param  TShirtSize|null  $tShirtSize
     * @param  string|null  $nfcTagId
     * @param  string|null  $miniIdentifier
     * @param  string|null  $password
     * @param  string|null  $fireBaseToken
     * @param  string|null  $badgeFile
     * @param  Product|null  $overrideBadgeProduct
     * @param  RoleEnum[]  $roles
     * @param  Collection<array-key, CheckIn>|null  $checkIns
     * @param  Collection<array-key, Achievement>|null  $achievements
     * @param  Collection<array-key, PrintJob>|null  $printJobs
     */
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
        private ?Product    $overrideBadgeProduct = null,
        private int         $points = 0,
        private int         $achievementsCompletedTime = 0,
        private ?int        $position = null,
        private array       $roles = [RoleEnum::USER],
        ?Collection         $checkIns = null,
        ?Collection         $achievements = null,
        ?Collection         $printJobs = null,
    ) {
        $this->checkIns                 = $checkIns ?? new ArrayCollection();
        $this->achievements             = $achievements ?? new ArrayCollection();
        $this->printJobs                = $printJobs ?? new ArrayCollection();
        $this->timetableItems           = new ArrayCollection();
        $this->characterQuizSubmissions = new ArrayCollection();
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
     * @return Collection<array-key, CheckIn>
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

    public function getBadgeFile(): ?string
    {
        return $this->badgeFile;
    }

    public function setBadgeFile(?string $badgeFile): self
    {
        if ($this->badgeFile !== $badgeFile) {
            if ($this->badgeFile !== null && file_exists($this->badgeFile)) {
                unlink($this->badgeFile);
            }

            $this->badgeFile = $badgeFile;
        }

        return $this;
    }

    public function getOverrideBadgeProduct(): ?Product
    {
        return $this->overrideBadgeProduct;
    }

    public function setOverrideBadgeProduct(?Product $overrideBadgeProduct): self
    {
        $this->overrideBadgeProduct = $overrideBadgeProduct;

        return $this;
    }

    public function getPoints(): int
    {
        return $this->points;
    }

    public function setPoints(int $points): self
    {
        $this->points = $points;
        return $this;
    }

    public function getAchievementsCompletedTime(): int
    {
        return $this->achievementsCompletedTime;
    }

    public function setAchievementsCompletedTime(int $achievementsCompletedTime): self
    {
        $this->achievementsCompletedTime = $achievementsCompletedTime;
        return $this;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(?int $position): self
    {
        $this->position = $position;
        return $this;
    }

    /**
     * @param  RoleEnum[]  $roles
     * @return $this
     */
    public function setRoles(array $roles): self
    {
        $this->roles = $roles;
        return $this;
    }

    /**
     * @return string[]
     */
    public function getRoles(): array
    {
        $roles = [];
        foreach ($this->roles as $role) {
            $roles = [...$roles, ...$role->getRoles()];
        }

        return array_map(static fn(RoleEnum $role) => $role->value, RoleEnum::deduplicate($roles));
    }

    /**
     * @return RoleEnum[]
     */
    public function getUserRoles(): array
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
     * @return Collection<array-key, AttendeeAchievement>
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

    /**
     * @return Collection<array-key, PrintJob>
     */
    public function getPrintJobs(): Collection
    {
        return $this->printJobs;
    }

    public function addPrintJob(PrintJob $printJob): self
    {
        if (!$this->printJobs->contains($printJob)) {
            $this->printJobs->add($printJob);
        }

        return $this;
    }

    public function removePrintJob(PrintJob $printJob): self
    {
        if ($this->printJobs->contains($printJob)) {
            $this->printJobs->removeElement($printJob);
        }

        return $this;
    }

    /**
     * @return Collection<array-key, TimetableItem>
     */
    public function getTimetableItems(): Collection
    {
        return $this->timetableItems;
    }

    public function addTimetableItem(TimetableItem $timetableItem): self
    {
        if (!$this->timetableItems->contains($timetableItem) && $timetableItem->getVolunteer() === $this) {
            $this->timetableItems->add($timetableItem);
        }

        return $this;
    }

    public function removeTimetableItem(TimetableItem $timetableItem): self
    {
        if ($this->timetableItems->contains($timetableItem)) {
            $this->timetableItems->removeElement($timetableItem);
        }

        return $this;
    }

    /**
     * @return Collection<array-key, CharacterQuizSubmission>
     */
    public function getCharacterQuizSubmissions(): Collection
    {
        return $this->characterQuizSubmissions;
    }

    public function addCharacterQuizSubmission(CharacterQuizSubmission $characterQuizSubmission): self
    {
        if (
            !$this->characterQuizSubmissions->contains($characterQuizSubmission)
            && $characterQuizSubmission->getAttendee() === $this
        ) {
            $this->characterQuizSubmissions->add($characterQuizSubmission);
        }

        return $this;
    }

    public function removeCharacterQuizSubmission(CharacterQuizSubmission $characterQuizSubmission): self
    {
        if ($this->characterQuizSubmissions->contains($characterQuizSubmission)) {
            $this->characterQuizSubmissions->removeElement($characterQuizSubmission);
        }

        return $this;
    }

    public function getLastCharacterQuiz(): ?CharacterQuizSubmission
    {
        $lastCharacterQuiz = null;
        foreach ($this->getCharacterQuizSubmissions() as $characterQuizSubmission) {
            if (
                $lastCharacterQuiz === null ||
                $characterQuizSubmission->getCreatedAt() > $lastCharacterQuiz->getCreatedAt()
            ) {
                $lastCharacterQuiz = $characterQuizSubmission;
            }
        }

        return $lastCharacterQuiz;
    }
}
