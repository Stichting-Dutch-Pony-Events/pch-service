<?php

namespace App\Application\View;

use App\Application\View\Trait\EntityViewTrait;
use App\Domain\Enum\TShirtSize;
use App\Security\Enum\RoleEnum;
use App\Util\SymfonyUtils\Attribute\MapsMany;
use JMS\Serializer\Annotation\Type;

class AttendeeView
{
    use EntityViewTrait;

    public function __construct(
        public string       $name,
        public ?string      $firstName,
        public ?string      $middleName,
        public ?string      $familyName,
        public ?string      $nickName,
        public ?string      $email,
        public string       $orderCode,
        public int          $ticketId,
        public ProductView  $product,
        public ?string      $nfcTagId,
        public ?string      $miniIdentifier,
        public ?TShirtSize  $tShirtSize,
        public ?string      $fireBaseToken,
        public ?ProductView $overrideBadgeProduct,

        /** @var RoleEnum[] $userRoles */
        #[Type('array<' . RoleEnum::class . '>')]
        public ?array       $userRoles,

        public ?TeamView    $team,

        /** @var AttendeeAchievementView[] $achievements */
        #[Type('array<' . AttendeeAchievementView::class . '>')]
        #[MapsMany(AttendeeAchievementView::class)]
        public array        $achievements
    ) {
    }
}
