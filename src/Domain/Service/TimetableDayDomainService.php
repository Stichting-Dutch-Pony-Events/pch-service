<?php

namespace App\Domain\Service;

use App\Application\Request\TimetableDayRequest;
use App\DataAccessLayer\Repository\TimetableDayRepository;
use App\Domain\Entity\TimetableDay;

readonly class TimetableDayDomainService
{
    public function __construct(
        private TimetableDayRepository $timetableDayRepository
    ) {
    }

    public function createTimetableDay(TimetableDayRequest $timetableDayRequest): TimetableDay
    {
        return new TimetableDay(
            title: $timetableDayRequest->title,
            startsAt: $timetableDayRequest->startsAt,
            endsAt: $timetableDayRequest->endsAt,
            order: $this->timetableDayRepository->getNextOrder()
        );
    }

    public function updateTimetableDay(
        TimetableDay        $timetableDay,
        TimetableDayRequest $timetableDayRequest
    ): TimetableDay {
        $timetableDay->setTitle($timetableDayRequest->title);
        $timetableDay->setStartsAt($timetableDayRequest->startsAt);
        $timetableDay->setEndsAt($timetableDayRequest->endsAt);

        return $timetableDay;
    }
}