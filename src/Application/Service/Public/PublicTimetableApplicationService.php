<?php

namespace App\Application\Service\Public;

use App\Application\Response\PublicTimetableResponse;
use App\Application\View\PublicTimetable\TimetableDayView;
use App\Application\View\PublicTimetable\TimetableLocationView;
use App\DataAccessLayer\Repository\TimetableDayRepository;
use App\DataAccessLayer\Repository\TimetableLocationRepository;
use App\Util\SymfonyUtils\Mapper;
use Psr\Cache\InvalidArgumentException;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

readonly class PublicTimetableApplicationService
{
    public function __construct(
        private TimetableLocationRepository $timetableLocationRepository,
        private TimetableDayRepository      $timetableDayRepository,
        private CacheInterface              $cache,
    ) {
    }

    /**
     * @throws InvalidArgumentException
     */
    public function getPublicTimeTable(): PublicTimetableResponse
    {
        return $this->cache->get('public_timetable', function (ItemInterface $item): PublicTimetableResponse {
            $item->expiresAfter(3600);

            $timetableLocations = $this->timetableLocationRepository->getPublicTimetableLocations();

            $timetableDays = $this->timetableDayRepository->getOrdered();

            return new PublicTimetableResponse(
                Mapper::mapMany($timetableDays, TimetableDayView::class),
                Mapper::mapMany($timetableLocations, TimetableLocationView::class)
            );
        });
    }
}