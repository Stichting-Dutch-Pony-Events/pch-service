<?php

namespace App\Application\Service;

use App\Application\Request\SettingRequest;
use App\Domain\Entity\Setting;
use App\Domain\Service\SettingDomainService;

readonly class SettingApplicationService
{
    public function __construct(
        private SettingDomainService $settingDomainService
    ) {
    }

    public function setSetting(SettingRequest $settingRequest): Setting
    {
        return $this->settingDomainService->setSetting($settingRequest->name, $settingRequest->value);
    }
}