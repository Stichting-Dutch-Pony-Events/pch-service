<?php

namespace App\Application\Service;

use App\Application\Request\CheckInListRequest;
use App\Domain\Entity\CheckInList;
use App\Domain\Service\CheckInListDomainService;
use Doctrine\ORM\EntityManagerInterface;

readonly class CheckInListApplicationService
{
    public function __construct(
        private EntityManagerInterface   $entityManager,
        private CheckInListDomainService $checkInListDomainService,
    )
    {
    }

    public function createCheckInList(CheckInListRequest $checkInListRequest): CheckInList {
        $checkInList = $this->checkInListDomainService->createCheckInList($checkInListRequest);

        $this->entityManager->persist($checkInList);
        $this->entityManager->flush();

        return $checkInList;
    }
}
