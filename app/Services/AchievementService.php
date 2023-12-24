<?php

namespace App\Services;

use App\Contracts\AchievementServiceInterface;
use App\Enums\BadgeEnum;
use App\Helpers\AchievementHelper;
use App\Models\User;
use Exception;

class AchievementService implements AchievementServiceInterface
{

    private $lessonsWatchedConfig;
    private $commentsWrittenConfig;

    public function __construct($lessonsWatchedConfig, $commentsWrittenConfig)
    {
        $this->lessonsWatchedConfig = $lessonsWatchedConfig;
        $this->commentsWrittenConfig = $commentsWrittenConfig;
    }

    private const INTERMEDIATE_THRESHOLD = 4;
    private const ADVANCED_THRESHOLD = 8;
    private const MASTER_THRESHOLD = 10;

    /**
     * Retrieves the achievements unlocked by the user.
     *
     * This method checks both lessons watched and comments written by the user
     * and compiles a list of all achievements unlocked.
     *
     * @param User $user The user whose achievements are to be checked.
     * @return array An array of unlocked achievement names.
     * @throws Exception
     */
    public function getUnlockedAchievements(User $user): array
    {
        return array_merge(
            $this->getLessonsWatchedAchievements($user),
            $this->getCommentsWrittenAchievements($user)
        );
    }

    /**
     * Calculates the next set of achievements the user can unlock.
     *
     * Determines which achievements in both lessons watched and comments written
     * are immediately achievable for the user based on their current progress.
     *
     * @param User $user The user whose next available achievements are to be calculated.
     * @return array An array of the next achievable achievement names.
     * @throws Exception
     */
    public function getNextAvailableAchievements(User $user): array
    {
        return array_merge(
            $this->getNextLessonsWatchedAchievement($user),
            $this->getNextCommentsWrittenAchievement($user)
        );
    }

    /**
     * Determines the current badge of the user based on unlocked achievements.
     *
     * This method counts the total number of achievements the user has unlocked
     * and determines their current badge level. The badge levels are defined based on
     * pre-set achievement thresholds.
     *
     * @param User $user The user whose current badge is to be determined.
     * @return string The name of the current badge the user has earned.
     * @throws Exception
     */
    public function getCurrentBadge(User $user): string
    {
        $achievementsCount = count($this->getUnlockedAchievements($user));
        return $this->determineBadgeByAchievementsCount($achievementsCount);
    }

    /**
     * Calculates the next badge the user can earn and the remaining achievements needed.
     *
     * This method evaluates the user's current progress in terms of achievements unlocked
     * and determines what the next badge level is. Additionally, it calculates how many more
     * achievements are needed for the user to reach this next badge level.
     *
     * @param User $user The user whose next badge information is to be calculated.
     * @return array An array containing the name of the next badge and the number of additional
     *               achievements required to unlock it. If the user has reached the highest badge,
     *               the next badge is returned as null and remaining achievements as 0.
     * @throws Exception
     */
    public function getNextBadgeInfo(User $user): array
    {
        $achievementsCount = count($this->getUnlockedAchievements($user));
        $currentBadge = $this->getCurrentBadge($user);
        $nextBadge = $this->determineNextBadge($achievementsCount);
        $remainingToUnlockNextBadge = $this->calculateRemainingForNextBadge($achievementsCount, $nextBadge);

        return [$nextBadge, $remainingToUnlockNextBadge];
    }

    private function getLessonsWatchedAchievements(User $user): array
    {
        $achievements = [];
        $watchedCount = $user->watched()->count();

        if (!$this->lessonsWatchedConfig) {
            throw new Exception("Configuration for lessons watched achievements is not set.");
        }

        foreach ($this->lessonsWatchedConfig as $key => $threshold) {
            if ($watchedCount >= $threshold) {
                $achievementEnumCase = AchievementHelper::mapKeyToAchievementEnum($key);
                $achievements[] = $achievementEnumCase->value;
            }
        }

        return $achievements;
    }

    private function getCommentsWrittenAchievements(User $user): array
    {
        $achievements = [];
        $commentsCount = $user->comments()->count();

        if (!$this->commentsWrittenConfig) {
            throw new Exception("Configuration for comments written achievements is not set.");
        }
        foreach ($this->commentsWrittenConfig as $key => $threshold) {
            if ($commentsCount >= $threshold) {
                $achievementEnumCase = AchievementHelper::mapKeyToAchievementEnum($key);
                $achievements[] = $achievementEnumCase->value;
            }
        }

        return $achievements;
    }

    private function getNextLessonsWatchedAchievement(User $user): array
    {
        $watchedCount = $user->watched()->count();
        $nextAchievements = [];

        if (!$this->lessonsWatchedConfig) {
            throw new Exception("Configuration for lessons watched achievements is not set.");
        }

        foreach ($this->lessonsWatchedConfig as $key => $threshold) {
            if ($watchedCount < $threshold) {
                $achievementEnumCase = AchievementHelper::mapKeyToAchievementEnum($key);
                $nextAchievements[] = $achievementEnumCase->value;
                break; // Exit loop after finding the immediate next achievement
            }
        }

        return $nextAchievements;
    }

    private function getNextCommentsWrittenAchievement(User $user): array
    {
        $commentsCount = $user->comments()->count();
        $nextAchievements = [];

        if (!$this->commentsWrittenConfig) {
            throw new Exception("Configuration for comments written achievements is not set.");
        }

        foreach ($this->commentsWrittenConfig as $key => $threshold) {
            if ($commentsCount < $threshold) {
                $achievementEnumCase = AchievementHelper::mapKeyToAchievementEnum($key);
                $nextAchievements[] = $achievementEnumCase->value;
                break; // Exit loop after finding the immediate next achievement
            }
        }

        return $nextAchievements;
    }

    private function determineNextBadge($achievementsCount): ?string
    {
        if ($achievementsCount < self::INTERMEDIATE_THRESHOLD) {
            return BadgeEnum::Intermediate->value;
        } elseif ($achievementsCount < self::ADVANCED_THRESHOLD) {
            return BadgeEnum::Advanced->value;
        } elseif ($achievementsCount < self::MASTER_THRESHOLD) {
            return BadgeEnum::Master->value;
        } else {
            return null;
        }
    }

    private function calculateRemainingForNextBadge($achievementsCount, $nextBadge)
    {
        if ($nextBadge === BadgeEnum::Intermediate->value) {
            return self::INTERMEDIATE_THRESHOLD - $achievementsCount;
        } elseif ($nextBadge === BadgeEnum::Advanced->value) {
            return self::ADVANCED_THRESHOLD - $achievementsCount;
        } elseif ($nextBadge === BadgeEnum::Master->value) {
            return self::MASTER_THRESHOLD - $achievementsCount;
        } else {
            return 0;
        }
    }

    private function determineBadgeByAchievementsCount($count): string
    {

        if ($count >= self::MASTER_THRESHOLD) {
            return BadgeEnum::Master->value;
        } elseif ($count >= self::ADVANCED_THRESHOLD) {
            return BadgeEnum::Advanced->value;
        } elseif ($count >= self::INTERMEDIATE_THRESHOLD) {
            return BadgeEnum::Intermediate->value;
        } else {
            return BadgeEnum::Beginner->value;
        }
    }

}
