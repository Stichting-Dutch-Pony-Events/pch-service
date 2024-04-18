<?php

namespace App\Application\Service;

use App\Application\Request\CheckInRequest;
use App\Application\Response\CheckInResponse;
use App\Application\View\AttendeeView;
use App\DataAccessLayer\Pretix\Repositories\CheckInRepository;
use App\DataAccessLayer\Pretix\Repositories\OrderRepository;
use App\DataAccessLayer\Pretix\Request\CheckInRequest as PretixCheckInRequest;
use App\DataAccessLayer\Repository\CheckInListRepository;
use App\Domain\Service\CheckInDomainService;
use App\Util\Exceptions\Exception\Entity\EntityNotFoundException;
use App\Util\SymfonyUtils\Mapper;
use Doctrine\ORM\EntityManagerInterface;

readonly class CheckInApplicationService
{
    public function __construct(
        private CheckInListRepository      $checkInListRepository,
        private CheckInRepository          $checkInRepository,
        private AttendeeApplicationService $attendeeApplicationService,
        private CheckInDomainService       $checkInDomainService,
        private EntityManagerInterface     $entityManager,
        private OrderRepository            $orderRepository
    ) {
    }

    public function performCheckIn(CheckInRequest $checkInRequest): ?CheckInResponse
    {
        $activeCheckInList = $this->checkInListRepository->findActiveCheckInList($checkInRequest->listType);
        if (!isset($activeCheckInList)) {
            throw new EntityNotFoundException('No Active Check-In List Found');
        }

        $pretixCheckInRequest = new PretixCheckInRequest($checkInRequest->secret, [$activeCheckInList->getPretixId()]);
        $checkIn              = $this->checkInRepository->checkIn($pretixCheckInRequest);

        $checkInResponse = new CheckInResponse(
            status: $checkIn->getStatus(),
            errorReason: $checkIn->getErrorReason(),
            reasonExplanation: $checkIn->getReasonExplanation(),
            requiresAttention: $checkIn->isRequiresAttention()
        );

        if ($checkIn->getOrderPosition() !== null) {
            $attendee = $this->attendeeApplicationService->createAttendeeFromOrderPosition(
                $checkIn->getOrderPosition(),
                $this->orderRepository->getOrderByCode($checkIn->getOrderPosition()->getOrder())
            );

            $checkInResponse->attendee = Mapper::mapOne($attendee, AttendeeView::class);

            $checkInEntity = $this->checkInDomainService->createCheckIn(
                $checkInResponse,
                $attendee,
                $activeCheckInList
            );

            $this->entityManager->persist($checkInEntity);
            $this->entityManager->flush();
        }

        return $checkInResponse;
    }
}
