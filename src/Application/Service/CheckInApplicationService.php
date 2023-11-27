<?php

namespace App\Application\Service;

use App\Application\Request\CheckInRequest;
use App\Application\Response\CheckInResponse;
use App\Application\View\AttendeeView;
use App\DataAccessLayer\Pretix\Enum\CheckInStatus;
use App\DataAccessLayer\Pretix\Repositories\CheckInRepository;
use App\DataAccessLayer\Pretix\Request\CheckInRequest as PretixCheckInRequest;
use App\DataAccessLayer\Repository\CheckInListRepository;
use App\Util\Exceptions\Exception\Entity\EntityNotFoundException;
use App\Util\SymfonyUtils\Mapper;

readonly class CheckInApplicationService
{
    public function __construct(
        private CheckInListRepository      $checkInListRepository,
        private CheckInRepository          $checkInRepository,
        private AttendeeApplicationService $attendeeApplicationService
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

        if ($checkIn->getStatus() === CheckInStatus::OK) {
            $attendee = $this->attendeeApplicationService->createAttendeeFromOrderPosition(
                $checkIn->getOrderPosition()
            );

            $checkInResponse->attendee = Mapper::mapOne($attendee, AttendeeView::class);
        }

        return $checkInResponse;
    }
}
