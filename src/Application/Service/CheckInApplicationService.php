<?php

namespace App\Application\Service;

use App\Application\Enum\BadgeSourceEnum;
use App\Application\Request\CheckInRequest;
use App\Application\Response\CheckInResponse;
use App\Application\View\AttendeeView;
use App\DataAccessLayer\Pretix\Enum\CheckInErrorReason;
use App\DataAccessLayer\Pretix\Enum\CheckInStatus;
use App\DataAccessLayer\Pretix\Repositories\CheckInRepository;
use App\DataAccessLayer\Pretix\Repositories\OrderRepository;
use App\DataAccessLayer\Pretix\Request\CheckInRequest as PretixCheckInRequest;
use App\DataAccessLayer\Pretix\Views\OrderPosition;
use App\DataAccessLayer\Repository\AttendeeRepository;
use App\DataAccessLayer\Repository\CheckInListRepository;
use App\Domain\Entity\Attendee;
use App\Domain\Entity\CheckInList;
use App\Domain\Enum\CheckInListType;
use App\Domain\Enum\PrintJobStatusEnum;
use App\Domain\Service\CheckInDomainService;
use App\Domain\Service\PrintJobDomainService;
use App\Util\Exceptions\Exception\Entity\EntityNotFoundException;
use App\Util\SymfonyUtils\Mapper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

readonly class CheckInApplicationService
{
    public function __construct(
        private CheckInListRepository      $checkInListRepository,
        private CheckInRepository          $checkInRepository,
        private AttendeeApplicationService $attendeeApplicationService,
        private AttendeeRepository         $attendeeRepository,
        private CheckInDomainService       $checkInDomainService,
        private EntityManagerInterface     $entityManager,
        private OrderRepository            $orderRepository,
        private ParameterBagInterface      $parameterBag,
        private PrintJobDomainService      $printJobDomainService
    ) {
    }

    public function performCheckIn(CheckInRequest $checkInRequest): ?CheckInResponse
    {
        $activeCheckInList = $this->checkInListRepository->findActiveCheckInList($checkInRequest->listType);
        if (!isset($activeCheckInList)) {
            throw new EntityNotFoundException('No Active Check-In List Found');
        }

        if ($checkInRequest->listType === CheckInListType::MERCH && $checkInRequest->merchPreCheckIn) {
            return $this->merchPreCheckin($checkInRequest, $activeCheckInList);
        }

        if (!$this->parameterBag->get('app.live_mode')) {
            return $this->performDummyCheckIn($checkInRequest, $activeCheckInList);
        }

        $pretixCheckInRequest = new PretixCheckInRequest($checkInRequest->secret, [$activeCheckInList->getPretixId()]);
        $checkIn = $this->checkInRepository->checkIn($pretixCheckInRequest);

        $checkInResponse = new CheckInResponse(
            status: $checkIn->getStatus(),
            errorReason: $checkIn->getErrorReason(),
            reasonExplanation: $checkIn->getReasonExplanation(),
            requiresAttention: $checkIn->isRequiresAttention()
        );

        if ($checkIn->getOrderPosition() !== null) {
            $checkInResponse = $this->checkInAttendee(
                $checkIn->getOrderPosition(),
                $checkInResponse,
                $activeCheckInList
            );
        }

        return $checkInResponse;
    }

    private function merchPreCheckin(CheckInRequest $checkInRequest, CheckInList $checkInList): ?CheckInResponse
    {
        $attendee = $this->attendeeRepository->findOneBy(['ticketSecret' => $checkInRequest->secret]);

        if (!isset($attendee)) {
            return new CheckInResponse(
                status: CheckInStatus::ERROR,
                errorReason: CheckInErrorReason::INVALID,
                reasonExplanation: 'Attendee not found'
            );
        }

        if (!$checkInList->getProducts()->contains($attendee->getProduct())) {
            return new CheckInResponse(
                status: CheckInStatus::ERROR,
                errorReason: CheckInErrorReason::PRODUCT,
                reasonExplanation: 'Product doesn\'t have merch'
            );
        }

        foreach ($attendee->getCheckIns() as $checkIn) {
            if ($checkIn->getCheckInList() === $checkInList) {
                return new CheckInResponse(
                    status: CheckInStatus::ERROR,
                    errorReason: CheckInErrorReason::ALREADY_REDEEMED,
                    reasonExplanation: 'Merch already redeemed'
                );
            }
        }

        return new CheckInResponse(
            status: CheckInStatus::OK,
            errorReason: null,
            attendee: Mapper::mapOne($attendee, AttendeeView::class),
        );
    }

    private function performDummyCheckIn(CheckInRequest $checkInRequest, CheckInList $checkInList): ?CheckInResponse
    {
        $orderPosition = $this->checkInRepository->search($checkInRequest->secret, $checkInList->getPretixId());

        $checkInResponse = new CheckInResponse(
            status: CheckInStatus::OK,
            errorReason: null,
            reasonExplanation: null,
            requiresAttention: false
        );

        if ($orderPosition) {
            $checkInResponse = $this->checkInAttendee($orderPosition, $checkInResponse, $checkInList);
        } else {
            $checkInResponse->status = CheckInStatus::ERROR;
            $checkInResponse->errorReason = CheckInErrorReason::INVALID;
        }

        return $checkInResponse;
    }

    private function checkInAttendee(
        OrderPosition   $position,
        CheckInResponse $checkInResponse,
        CheckInList     $checkInList
    ): CheckInResponse {
        $attendee = $this->attendeeApplicationService->createAttendeeFromOrderPosition(
            $position,
            $this->orderRepository->getOrderByCode($position->getOrder())
        );

        $checkInResponse->attendee = Mapper::mapOne($attendee, AttendeeView::class);
        $checkInResponse->badgeSource = $this->createPrintJobIfNeeded($attendee);

        $checkInEntity = $this->checkInDomainService->createCheckIn(
            $checkInResponse,
            $attendee,
            $checkInList
        );

        if ($checkInEntity) {
            $this->entityManager->persist($checkInEntity);
        }
        $this->entityManager->flush();

        return $checkInResponse;
    }

    private function createPrintJobIfNeeded(Attendee $attendee): BadgeSourceEnum
    {
        $needPrintJob = true;

        foreach ($attendee->getPrintJobs() as $job) {
            if ($job->getStatus() === PrintJobStatusEnum::COMPLETED) {
                $needPrintJob = false;

                return BadgeSourceEnum::BADGE_STASH;
            }

            if ($job->getStatus() === PrintJobStatusEnum::PRINTING || $job->getStatus(
                ) === PrintJobStatusEnum::PENDING) {
                $needPrintJob = false;
            }
        }

        if ($needPrintJob) {
            $printJob = $this->printJobDomainService->createPrintJob($attendee);

            $this->entityManager->persist($printJob);
        }

        return BadgeSourceEnum::PRINTER;
    }
}
