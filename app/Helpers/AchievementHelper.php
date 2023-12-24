<?php

namespace App\Helpers;

use App\Enums\AchievementEnum;
use InvalidArgumentException;

class AchievementHelper
{
    /**
     * Map a key to the corresponding AchievementEnum value.
     *
     * @param string $key The key representing the achievement.
     * @return AchievementEnum The corresponding AchievementEnum value.
     * @throws InvalidArgumentException If the key is invalid.
     */
    public static function mapKeyToAchievementEnum(string $key): AchievementEnum
    {
        return match ($key) {
            'FirstLessonWatched' => AchievementEnum::FirstLessonWatched,
            'FiveLessonsWatched' => AchievementEnum::FiveLessonsWatched,
            'TenLessonsWatched' => AchievementEnum::TenLessonsWatched,
            'TwentyFiveLessonsWatched' => AchievementEnum::TwentyFiveLessonsWatched,
            'FiftyLessonsWatched' => AchievementEnum::FiftyLessonsWatched,
            'FirstCommentWritten' => AchievementEnum::FirstCommentWritten,
            'ThreeCommentsWritten' => AchievementEnum::ThreeCommentsWritten,
            'FiveCommentsWritten' => AchievementEnum::FiveCommentsWritten,
            'TenCommentsWritten' => AchievementEnum::TenCommentsWritten,
            'TwentyCommentsWritten' => AchievementEnum::TwentyCommentsWritten,
            default => throw new InvalidArgumentException("Invalid achievement key: $key"),
        };
    }
}
