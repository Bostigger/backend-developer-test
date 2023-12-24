<?php

namespace App;

namespace App\Traits;

use App\Events\AchievementUnlocked;
use App\Events\BadgeUnlocked;
use App\Helpers\AchievementHelper;
use App\Services\AchievementService;

trait AchievementHandlerTrait
{
    /**
     * Get the instance of AchievementService.
     *
     * @return AchievementService
     */
    abstract protected function getAchievementService(): AchievementService;

    /**
     * Check and unlock achievements based on the type and count.
     *
     * @param mixed $user The user for whom achievements are being checked.
     * @param int $count The count of lessons watched or comments written.
     * @param string $type The type of achievement ('lessons_watched' or 'comments_written').
     * @return void
     */
    protected function checkAndUnlockAchievements($user, int $count, string $type): void
    {

        $config = config("achievements.{$type}");

        foreach ($config as $key => $threshold) {
            if ($count === $threshold) {
                $achievementEnumCase = AchievementHelper::mapKeyToAchievementEnum($key);
                $achievementName = $achievementEnumCase->value;
                event(new AchievementUnlocked($achievementName, $user));
                break;
            }
        }
    }

    /**
     * Update the user's badge based on their achievements.
     *
     * @param mixed $user The user for whom the badge is being updated.
     * @return void
     */
    protected function updateBadge($user): void
    {
        $achievementService = $this->getAchievementService();
        $currentBadge = $achievementService->getCurrentBadge($user);
        $nextBadgeInfo = $achievementService->getNextBadgeInfo($user);

        if ($nextBadgeInfo[0] !== $currentBadge) {
            event(new BadgeUnlocked($nextBadgeInfo[0], $user));
        }
    }


}
