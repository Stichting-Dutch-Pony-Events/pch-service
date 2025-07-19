<?php

namespace App\Application\Service;

use App\Application\Request\ChangeOrderRequest;
use App\Application\Request\TimetableLocationRequest;
use App\DataAccessLayer\Repository\TimetableDayRepository;
use App\DataAccessLayer\Repository\TimetableLocationRepository;
use App\Domain\Entity\TimetableLocation;
use App\Domain\Service\TimetableLocationDomainService;
use App\Util\Exceptions\Exception\Entity\EntityNotFoundException;
use Doctrine\ORM\EntityManagerInterface;

readonly class TimetableLocationApplicationService
{
    public function __construct(
        private EntityManagerInterface         $entityManager,
        private TimetableDayRepository         $timetableDayRepository,
        private TimetableLocationRepository    $timetableLocationRepository,
        private TimetableLocationDomainService $timetableLocationDomainService,
    ) {
    }

    public function createTimetableLocation(TimetableLocationRequest $timetableLocationRequest): TimetableLocation
    {
        return $this->entityManager->wrapInTransaction(function () use ($timetableLocationRequest): TimetableLocation {
            $timetableDays = [];
            foreach ($timetableLocationRequest->timetableDays as $timetableDayId) {
                $timetableDay = $this->timetableDayRepository->find($timetableDayId);
                if ($timetableDay === null) {
                    throw new EntityNotFoundException("Timetable Day not found.");
                }
                $timetableDays[] = $timetableDay;
            }

            $timetableLocation = $this->timetableLocationDomainService->createTimetableLocation(
                $timetableLocationRequest,
                $timetableDays
            );

            $this->entityManager->persist($timetableLocation);

            return $timetableLocation;
        });
    }

    public function updateTimetableLocation(
        TimetableLocation        $timetableLocation,
        TimetableLocationRequest $timetableLocationRequest
    ): TimetableLocation {
        return $this->entityManager->wrapInTransaction(
            function () use ($timetableLocation, $timetableLocationRequest): TimetableLocation {
                $timetableDays = [];
                foreach ($timetableLocationRequest->timetableDays as $timetableDayId) {
                    $timetableDay = $this->timetableDayRepository->find($timetableDayId);
                    if ($timetableDay === null) {
                        throw new EntityNotFoundException("Timetable Day not found.");
                    }
                    $timetableDays[] = $timetableDay;
                }

                return $this->timetableLocationDomainService->updateTimetableLocation(
                    $timetableLocation,
                    $timetableLocationRequest,
                    $timetableDays
                );
            }
        );
    }

    public function changeOrder(ChangeOrderRequest $changeOrderRequest): void
    {
        $this->entityManager->wrapInTransaction(function () use ($changeOrderRequest): void {
            foreach ($changeOrderRequest->ids as $index => $indexValue) {
                $timetableLocation = $this->timetableLocationRepository->find($indexValue);
                if (!$timetableLocation) {
                    throw new EntityNotFoundException('Timetable Day not found');
                }

                $timetableLocation->setOrder($index + 1);
            }
        });
    }

    public function deleteTimetableLocation(TimetableLocation $timetableLocation): void
    {
        $this->entityManager->wrapInTransaction(function () use ($timetableLocation): void {
            $this->entityManager->remove($timetableLocation);
        });
    }
}