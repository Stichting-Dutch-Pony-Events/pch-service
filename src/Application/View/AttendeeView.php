<?php

namespace App\Application\View;

use App\Domain\Entity\Achievement;
use App\Domain\Enum\TShirtSize;
use App\Util\SymfonyUtils\Attribute\MapsMany;
use DateTime;
use JMS\Serializer\Annotation\Type;

class AttendeeView
{
    public function __construct(
        public ?string     $id,
        public string      $name,
        public ?string     $firstName,
        public ?string     $middleName,
        public ?string     $familyName,
        public ?string     $nickName,
        public ?string     $email,
        public string      $orderCode,
        public int         $ticketId,
        public ProductView $product,
        public DateTime    $createdAt,
        public DateTime    $updatedAt,
        public ?string     $nfcTagId,
        public ?string     $miniIdentifier,
        public ?TShirtSize $tShirtSize,
        public ?string     $fireBaseToken,
        public ?array      $roles,
        public ?TeamView   $team,

        /** @var AttendeeAchievementView[] $achievements */
        #[Type('array<' . AttendeeAchievementView::class . '>')]
        #[MapsMany(AttendeeAchievementView::class)]
        public array       $achievements
    ) {
    }
}
