<?php

namespace App\Application\Service;

use App\Application\Request\SettingRequest;
use App\Domain\Entity\Setting;
use App\Domain\Service\SettingDomainService;
use Doctrine\ORM\EntityManagerInterface;

readonly class SettingApplicationService
{
    public function __construct(
        private SettingDomainService $settingDomainService,
        private EntityManagerInterface $entityManager
    ) {
    }

    public function setSetting(SettingRequest $settingRequest): Setting
    {
        $setting = $this->settingDomainService->setSetting($settingRequest->name, $settingRequest->value);

        $this->entityManager->flush();

        return $setting;
    }
}