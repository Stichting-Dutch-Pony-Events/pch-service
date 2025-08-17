<?php

namespace App\Application\Service;

use App\Application\Request\AttendeeRequest;
use App\Application\Request\SetAttendeeRolesRequest;
use App\Application\Request\SetPasswordRequest;
use App\DataAccessLayer\Pretix\Views\Order;
use App\DataAccessLayer\Pretix\Views\OrderPosition;
use App\DataAccessLayer\Repository\AttendeeRepository;
use App\DataAccessLayer\Repository\ProductRepository;
use App\DataAccessLayer\Repository\SettingRepository;
use App\Domain\Entity\Attendee;
use App\Domain\Entity\Setting;
use App\Domain\Enum\TShirtSize;
use App\Domain\Service\AttendeeDomainService;
use App\Domain\Service\TeamDomainService;
use App\Util\BadgeGenerator;
use App\Util\Exceptions\Exception\Entity\EntityNotFoundException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Filesystem\Filesystem;

readonly class AttendeeApplicationService
{
    public function __construct(
        private ProductRepository      $productRepository,
        private AttendeeDomainService  $attendeeDomainService,
        private AttendeeRepository     $attendeeRepository,
        private EntityManagerInterface $entityManager,
        private SettingRepository      $settingRepository,
        private TeamDomainService      $teamDomainService,
        private Filesystem             $filesystem,
        private BadgeGenerator         $badgeGenerator,
    ) {
    }

    public function createAttendeeFromOrderPosition(OrderPosition $orderPosition, Order $order): Attendee
    {
        $product = $this->productRepository->findByPretixId($orderPosition->getItemId());
        if (!isset($product)) {
            throw new EntityNotFoundException('Product not found');
        }

        $attendee = $this->attendeeRepository->findOneBy(['ticketId' => $orderPosition->getId()]);
        $shirtSize = $orderPosition->getAnswer('t-shirt-size');

        $attendeeRequest = new AttendeeRequest(
            name:           $orderPosition->getAttendeeName() ?? '',
            firstName:      $orderPosition->getAttendeeNamePart('given'),
            middleName:     $orderPosition->getAttendeeNamePart('middle'),
            familyName:     $orderPosition->getAttendeeNamePart('family'),
            nickName:       $orderPosition->getAnswer('nickname'),
            email:          $orderPosition->getAttendeeEmail() ?? $order->email,
            orderCode:      $orderPosition->getOrder(),
            ticketId:       $orderPosition->getId(),
            ticketSecret:   $orderPosition->getSecret(),
            productId:      $product->getId(),
            nfcTagId:       null,
            miniIdentifier: $this->attendeeRepository->getFreeMiniIdentifier(),
            tShirtSize:     $shirtSize !== null ? TShirtSize::tryFrom(strtolower($shirtSize)) : null,
        );

        if (isset($attendee)) {
            $this->attendeeDomainService->updateAttendee($attendee, $attendeeRequest);
        } else {
            $attendee = $this->attendeeDomainService->createAttendee($attendeeRequest, $product);

            if ($this->shouldAutoAssignTeam() && $attendee->getTeam() === null) {
                $this->teamDomainService->assignAttendeesToTeam([$attendee]);
            }

            $this->entityManager->persist($attendee);
        }

        $this->entityManager->flush();

        return $attendee;
    }

    /**
     * @throws EntityNotFoundException
     */
    public function updateAttendee(Attendee $attendee, AttendeeRequest $attendeeRequest): Attendee
    {
        $overrideBadgeProduct = null;
        if ($attendeeRequest->overrideBadgeProductId !== null) {
            $overrideBadgeProduct = $this->productRepository->find($attendeeRequest->overrideBadgeProductId);
            if (!$overrideBadgeProduct) {
                throw new EntityNotFoundException('Override badge product not found');
            }
        }

        $this->attendeeDomainService->updateAttendee($attendee, $attendeeRequest, $overrideBadgeProduct);

        $this->entityManager->flush();

        return $attendee;
    }

    public function updatePassword(Attendee $attendee, SetPasswordRequest $passwordRequest): Attendee
    {
        $attendee = $this->attendeeDomainService->updatePassword($attendee, $passwordRequest);

        $this->entityManager->flush();

        return $attendee;
    }

    public function shouldAutoAssignTeam(): bool
    {
        $setting = $this->settingRepository->findOneBy(['name' => 'auto-assign-teams']);

        return $setting instanceof Setting && $setting->getValue() === '1';
    }

    /**
     * @throws EntityNotFoundException
     */
    public function getAttendeeBadge(Attendee $attendee, bool $cache = true): string
    {
        if ($cache && $attendee->getBadgeFile() !== null && $this->filesystem->exists($attendee->getBadgeFile())) {
            return $this->filesystem->readFile($attendee->getBadgeFile());
        }

        $badgeFile = $this->badgeGenerator->generate($attendee);
        $this->entityManager->flush();

        return $this->filesystem->readFile($badgeFile);
    }

    public function find(string $identifier): Attendee
    {
        $attendee = $this->attendeeRepository->findAttendeeByIdentifier($identifier);

        if (!$attendee instanceof Attendee) {
            throw new EntityNotFoundException('Attendee not found');
        }

        return $attendee;
    }

    public function setAttendeeRoles(Attendee $attendee, SetAttendeeRolesRequest $setAttendeeRolesRequest): Attendee
    {
        $attendee = $this->attendeeDomainService->setAttendeeRoles($attendee, $setAttendeeRolesRequest);

        $this->entityManager->flush();

        return $attendee;
    }
}
