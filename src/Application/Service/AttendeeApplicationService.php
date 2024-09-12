<?php

namespace App\Application\Service;

use App\Application\Request\AttendeeRequest;
use App\Application\Request\SetPasswordRequest;
use App\DataAccessLayer\Pretix\Views\Order;
use App\DataAccessLayer\Pretix\Views\OrderPosition;
use App\DataAccessLayer\Repository\AttendeeRepository;
use App\DataAccessLayer\Repository\ProductRepository;
use App\Domain\Entity\Attendee;
use App\Domain\Enum\TShirtSize;
use App\Domain\Service\AttendeeDomainService;
use App\Util\Exceptions\Exception\Entity\EntityNotFoundException;
use Doctrine\ORM\EntityManagerInterface;

readonly class AttendeeApplicationService
{
    public function __construct(
        private ProductRepository      $productRepository,
        private AttendeeDomainService  $attendeeDomainService,
        private AttendeeRepository     $attendeeRepository,
        private EntityManagerInterface $entityManager
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
            name: $orderPosition->getAttendeeName(),
            firstName: $orderPosition->getAttendeeNamePart('given'),
            middleName: $orderPosition->getAttendeeNamePart('middle'),
            familyName: $orderPosition->getAttendeeNamePart('family'),
            nickName: $orderPosition->getAnswer('nickname'),
            email: $orderPosition->getAttendeeEmail() ?? $order->email,
            orderCode: $orderPosition->getOrder(),
            ticketId: $orderPosition->getId(),
            ticketSecret: $orderPosition->getSecret(),
            productId: $product->getId(),
            nfcTagId: null,
            miniIdentifier: $this->attendeeRepository->getFreeMiniIdentifier(),
            tShirtSize: $shirtSize !== null ? TShirtSize::tryFrom(strtolower($shirtSize)) : null,
        );

        if (isset($attendee)) {
            $this->attendeeDomainService->updateAttendee($attendee, $attendeeRequest);
        } else {
            $attendee = $this->attendeeDomainService->createAttendee($attendeeRequest, $product);

            $this->entityManager->persist($attendee);
        }

        $this->entityManager->flush();

        return $attendee;
    }

    public function updateAttendee(Attendee $attendee, AttendeeRequest $attendeeRequest): Attendee
    {
        $this->attendeeDomainService->updateAttendee($attendee, $attendeeRequest);

        $this->entityManager->flush();

        return $attendee;
    }

    public function updatePassword(Attendee $attendee, SetPasswordRequest $passwordRequest): Attendee
    {
        $attendee = $this->attendeeDomainService->updatePassword($attendee, $passwordRequest);

        $this->entityManager->flush();

        return $attendee;
    }
}
