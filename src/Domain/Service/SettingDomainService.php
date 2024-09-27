<?php

namespace App\Domain\Service;

use App\DataAccessLayer\Repository\SettingRepository;
use App\Domain\Entity\Setting;
use Doctrine\ORM\EntityManagerInterface;

readonly class SettingDomainService
{
    public function __construct(
        private SettingRepository $settingRepository,
        private EntityManagerInterface $entityManager
    ) {
    }

    public function setSetting(string $settingName, string $settingValue): Setting
    {
        $setting = $this->settingRepository->findOneBy(['name' => $settingName]);

        if (!$setting instanceof Setting) {
            $setting = new Setting($settingName, $settingValue);
            $this->entityManager->persist($setting);
        } else {
            $setting->setValue($settingValue);
        }

        return $setting;
    }
}