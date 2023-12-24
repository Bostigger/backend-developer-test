<?php

namespace App\Listeners;

use App\Events\LessonWatched;
use App\Services\AchievementService;
use App\Traits\AchievementHandlerTrait;

class HandleLessonWatched
{
    use AchievementHandlerTrait;

    private $achievementService;

    public function __construct(AchievementService $achievementService)
    {
        $this->achievementService = $achievementService;
    }

    public function handle(LessonWatched $event)
    {
        $user = $event->user;
        $watchedCount = $user->watched()->count();

        $this->checkAndUnlockAchievements($user, $watchedCount, 'lessons_watched');
        $this->updateBadge($user);
    }

    protected function getAchievementService()
    {
        return $this->achievementService;
    }
}
