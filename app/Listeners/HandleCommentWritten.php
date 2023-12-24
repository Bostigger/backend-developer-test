<?php

namespace App\Listeners;

use App\Events\CommentWritten;
use App\Services\AchievementService;
use App\Traits\AchievementHandlerTrait;

class HandleCommentWritten
{
    use AchievementHandlerTrait;

    private $achievementService;

    public function __construct(AchievementService $achievementService)
    {
        $this->achievementService = $achievementService;
    }

    public function handle(CommentWritten $event): void
    {
        $user = $event->comment->user;
        $commentsCount = $user->comments->count();

        $this->checkAndUnlockAchievements($user, $commentsCount, 'comments_written');
        $this->updateBadge($user);
    }

    protected function getAchievementService()
    {
        return $this->achievementService;
    }
}
