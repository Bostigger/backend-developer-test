<?php

namespace App\Contracts;

use App\Models\User;

interface AchievementServiceInterface
{
    public function getUnlockedAchievements(User $user);

    public function getNextAvailableAchievements(User $user);

    public function getCurrentBadge(User $user);

    public function getNextBadgeInfo(User $user);

}
