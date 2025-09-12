<?php

namespace App\Application\Service;

use App\Application\Request\ChangeOrderRequest;
use App\Application\Request\TimetableDayRequest;
use App\DataAccessLayer\Repository\TimetableDayRepository;
use App\Domain\Entity\TimetableDay;
use App\Domain\Service\TimetableDayDomainService;
use App\Util\Exceptions\Exception\Entity\EntityNotFoundException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Cache\CacheInterface;

readonly class TimetableDayApplicationService
{
    public function __construct(
        private TimetableDayRepository    $timetableDayRepository,
        private TimetableDayDomainService $timetableDayDomainService,
        private EntityManagerInterface    $entityManager,
        private CacheInterface            $cache
    ) {
    }

    public function createTimetableDay(TimetableDayRequest $timetableDayRequest): TimetableDay
    {
        return $this->entityManager->wrapInTransaction(function () use ($timetableDayRequest): TimetableDay {
            $timetableDay = $this->timetableDayDomainService->createTimetableDay($timetableDayRequest);

            $this->entityManager->persist($timetableDay);

            $this->cache->delete('public_timetable');

            return $timetableDay;
        });
    }

    public function updateTimetableDay(
        TimetableDay        $timetableDay,
        TimetableDayRequest $timetableDayRequest
    ): TimetableDay {
        return $this->entityManager->wrapInTransaction(
            function () use ($timetableDay, $timetableDayRequest): TimetableDay {
                $timetableDay = $this->timetableDayDomainService->updateTimetableDay(
                    $timetableDay,
                    $timetableDayRequest
                );

                $this->cache->delete('public_timetable');

                return $timetableDay;
            }
        );
    }

    public function changeOrder(ChangeOrderRequest $changeOrderRequest): void
    {
        $this->entityManager->wrapInTransaction(function () use ($changeOrderRequest): void {
            foreach ($changeOrderRequest->ids as $index => $indexValue) {
                $timetableDay = $this->timetableDayRepository->find($indexValue);
                if (!$timetableDay) {
                    throw new EntityNotFoundException('Timetable Day not found');
                }

                $timetableDay->setOrder($index + 1);
            }

            $this->cache->delete('public_timetable');
        });
    }

    public function deleteTimetableDay(TimetableDay $timetableDay): void
    {
        $this->entityManager->wrapInTransaction(function () use ($timetableDay): void {
            $this->entityManager->remove($timetableDay);

            $this->cache->delete('public_timetable');
        });
    }
}