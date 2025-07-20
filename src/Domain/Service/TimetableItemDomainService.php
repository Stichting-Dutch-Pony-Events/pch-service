<?php

namespace App\Domain\Service;

use App\Application\Request\TimetableItemRequest;
use App\Domain\Entity\Attendee;
use App\Domain\Entity\TimetableDay;
use App\Domain\Entity\TimetableItem;
use App\Domain\Entity\TimetableLocation;
use App\Domain\Enum\TimetableLocationType;
use App\Util\Exceptions\Exception\Common\InvalidInputException;

readonly class TimetableItemDomainService
{
    /**
     * @throws InvalidInputException
     */
    public function createTimetableItem(
        TimetableItemRequest $timetableItemRequest,
        TimetableDay         $timetableDay,
        TimetableLocation    $timetableLocation,
        ?Attendee            $volunteer = null
    ): TimetableItem {
        $this->checkInput($timetableItemRequest, $timetableDay, $timetableLocation, $volunteer);

        return new TimetableItem(
            timetableLocation: $timetableLocation,
            timetableDay: $timetableDay,
            title: $timetableItemRequest->title,
            startTime: $timetableItemRequest->startTime,
            endTime: $timetableItemRequest->endTime,
            description: $timetableItemRequest->description,
            volunteer: $volunteer,
        );
    }

    /**
     * @throws InvalidInputException
     */
    public function updateTimetableItem(
        TimetableItem $timetableItem,
        TimetableItemRequest $timetableItemRequest,
        ?Attendee $volunteer = null
    ): TimetableItem {
        $this->checkInput(
            $timetableItemRequest,
            $timetableItem->getTimetableDay(),
            $timetableItem->getTimetableLocation(),
            $volunteer
        );

        return $timetableItem
            ->setTitle($timetableItemRequest->title)
            ->setDescription($timetableItemRequest->description)
            ->setStartTime($timetableItemRequest->startTime)
            ->setEndTime($timetableItemRequest->endTime)
            ->setVolunteer($volunteer);
    }

    /**
     * @throws InvalidInputException
     */
    private function checkInput(
        TimetableItemRequest $timetableItemRequest,
        TimetableDay         $timetableDay,
        TimetableLocation    $timetableLocation,
        ?Attendee            $volunteer = null
    ): void {
        if ($volunteer !== null && $timetableLocation->getTimetableLocationType() === TimetableLocationType::ROOM) {
            throw new InvalidInputException("A volunteer cannot be assigned to a room timetable item.");
        }

        if ($timetableItemRequest->startTime >= $timetableItemRequest->endTime) {
            throw new InvalidInputException("Start time must be before end time.");
        }

        if ($timetableItemRequest->startTime < $timetableDay->getStartsAt() ||
            $timetableItemRequest->endTime > $timetableDay->getEndsAt()) {
            throw new InvalidInputException(
                "Timetable item times must be within the timetable day's start and end times."
            );
        }
    }
}