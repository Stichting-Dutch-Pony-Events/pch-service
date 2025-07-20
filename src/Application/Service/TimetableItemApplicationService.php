<?php

namespace App\Application\Service;

use App\Application\Request\TimetableItemRequest;
use App\DataAccessLayer\Repository\AttendeeRepository;
use App\DataAccessLayer\Repository\TimetableDayRepository;
use App\DataAccessLayer\Repository\TimetableLocationRepository;
use App\Domain\Entity\Attendee;
use App\Domain\Entity\TimetableDay;
use App\Domain\Entity\TimetableItem;
use App\Domain\Entity\TimetableLocation;
use App\Domain\Service\TimetableItemDomainService;
use App\Util\Exceptions\Exception\Entity\EntityNotFoundException;
use Doctrine\ORM\EntityManagerInterface;

readonly class TimetableItemApplicationService
{
    public function __construct(
        private TimetableItemDomainService  $timetableItemDomainService,
        private TimetableDayRepository      $timetableDayRepository,
        private TimetableLocationRepository $timetableLocationRepository,
        private AttendeeRepository          $attendeeRepository,
        private EntityManagerInterface      $entityManager,
    ) {
    }

    public function createTimetableItem(TimetableItemRequest $timetableItemRequest): TimetableItem
    {
        return $this->entityManager->wrapInTransaction(function () use ($timetableItemRequest): TimetableItem {
            $timetableDay = $this->timetableDayRepository->find($timetableItemRequest->timetableDayId);
            if (!$timetableDay instanceof TimetableDay) {
                throw new EntityNotFoundException("Timetable Day not found.");
            }

            $timetableLocation = $this->timetableLocationRepository->find($timetableItemRequest->timetableLocationId);
            if (!$timetableLocation instanceof TimetableLocation) {
                throw new EntityNotFoundException("Timetable Location not found.");
            }

            $volunteer = null;
            if ($timetableItemRequest->volunteerId !== null) {
                $volunteer = $this->attendeeRepository->find($timetableItemRequest->volunteerId);
                if (!$volunteer instanceof Attendee) {
                    throw new EntityNotFoundException("Volunteer not found.");
                }
            }

            $timetableItem = $this->timetableItemDomainService->createTimetableItem(
                $timetableItemRequest,
                $timetableDay,
                $timetableLocation,
                $volunteer
            );

            $this->entityManager->persist($timetableItem);

            return $timetableItem;
        });
    }

    public function updateTimetableItem(
        TimetableItem        $timetableItem,
        TimetableItemRequest $timetableItemRequest
    ): TimetableItem {
        return $this->entityManager->wrapInTransaction(
            function () use ($timetableItem, $timetableItemRequest): TimetableItem {
                $volunteer = null;
                if ($timetableItemRequest->volunteerId !== null) {
                    $volunteer = $this->attendeeRepository->find($timetableItemRequest->volunteerId);
                    if (!$volunteer instanceof Attendee) {
                        throw new EntityNotFoundException("Volunteer not found.");
                    }
                }

                return $this->timetableItemDomainService->updateTimetableItem(
                    $timetableItem,
                    $timetableItemRequest,
                    $volunteer
                );
            }
        );
    }

    public function deleteTimetableItem(TimetableItem $timetableItem): void
    {
        $this->entityManager->wrapInTransaction(function () use ($timetableItem): void {
            $this->entityManager->remove($timetableItem);
        });
    }
}