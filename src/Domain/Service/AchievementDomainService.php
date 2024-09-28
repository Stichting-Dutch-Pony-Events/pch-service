<?php

namespace App\Domain\Service;

use App\Domain\Entity\Achievement;
use App\Domain\Entity\Attendee;
use App\Domain\Entity\AttendeeAchievement;

class AchievementDomainService
{
    public function awardAchievement(Achievement $achievement, Attendee $attendee): AttendeeAchievement
    {
        foreach ($attendee->getAchievements() as $attendeeAchievement) {
            if ($attendeeAchievement->getAchievementId() === $achievement->getId()) {
                return $attendeeAchievement;
            }
        }

        return new AttendeeAchievement(
            achievement: $achievement,
            attendee: $attendee
        );
    }
}