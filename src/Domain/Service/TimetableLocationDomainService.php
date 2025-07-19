<?php

namespace App\Domain\Service;

use App\Application\Request\TimetableLocationRequest;
use App\DataAccessLayer\Repository\TimetableLocationRepository;
use App\Domain\Entity\TimetableDay;
use App\Domain\Entity\TimetableLocation;
use Doctrine\Common\Collections\ArrayCollection;

readonly class TimetableLocationDomainService
{
    public function __construct(
        private TimetableLocationRepository $timetableLocationRepository,
    ) {
    }

    /**
     * @param TimetableLocationRequest $timetableLocationRequest
     * @param TimetableDay[] $timetableDays
     * @return TimetableLocation
     */
    public function createTimetableLocation(
        TimetableLocationRequest $timetableLocationRequest,
        array                    $timetableDays
    ): TimetableLocation {
        return new TimetableLocation(
            title: $timetableLocationRequest->title,
            timetableLocationType: $timetableLocationRequest->timetableLocationType,
            order: $this->timetableLocationRepository->getNextOrder($timetableLocationRequest->timetableLocationType),
            timetableDays: new ArrayCollection($timetableDays)
        );
    }

    /**
     * @param TimetableLocation $timetableLocation
     * @param TimetableLocationRequest $timetableLocationRequest
     * @param TimetableDay[] $timetableDays
     * @return TimetableLocation
     */
    public function updateTimetableLocation(
        TimetableLocation        $timetableLocation,
        TimetableLocationRequest $timetableLocationRequest,
        array                    $timetableDays
    ): TimetableLocation {
        $timetableLocation->clearTimetableDays()
            ->setTitle($timetableLocationRequest->title)
            ->setTimetableLocationType($timetableLocationRequest->timetableLocationType);

        foreach ($timetableDays as $timetableDay) {
            $timetableLocation->addTimetableDay($timetableDay);
        }

        return $timetableLocation;
    }
}