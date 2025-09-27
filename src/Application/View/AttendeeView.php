<?php

namespace App\Application\View;

use App\Application\View\Trait\EntityViewTrait;
use App\Domain\Enum\TShirtSize;
use App\Security\Enum\RoleEnum;
use App\Util\SymfonyUtils\Attribute\MapsMany;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;

class AttendeeView
{
    use EntityViewTrait;

    /**
     * @param string $name
     * @param string|null $firstName
     * @param string|null $middleName
     * @param string|null $familyName
     * @param string|null $nickName
     * @param string|null $email
     * @param string $orderCode
     * @param int $ticketId
     * @param ProductView $product
     * @param string|null $nfcTagId
     * @param string|null $miniIdentifier
     * @param TShirtSize|null $tShirtSize
     * @param string|null $fireBaseToken
     * @param ProductView|null $overrideBadgeProduct
     * @param RoleEnum[]|null $userRoles
     * @param TeamView|null $team
     * @param AttendeeAchievementView[] $achievements
     */
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

        #[OA\Property(
            type: "array",
            items: new OA\Items(ref: new Model(type: RoleEnum::class)),
            nullable: true
        )]
        public ?array       $userRoles,

        public ?TeamView    $team,

        #[OA\Property(
            type: "array",
            items: new OA\Items(ref: new Model(type: AttendeeAchievementView::class))
        )]
        #[MapsMany(AttendeeAchievementView::class)]
        public array        $achievements
    ) {
    }
}
