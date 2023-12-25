<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\AchievementService;
use Exception;

class AchievementsController extends Controller
{
    protected AchievementService $achievementService;

    public function __construct(AchievementService $achievementService)
    {
        $this->achievementService = $achievementService;
    }

    /**
     * @throws Exception
     */
    public function index(User $user)
    {
        // Calculate the unlocked achievements
        $unlockedAchievements = $this->achievementService->getUnlockedAchievements($user);

        // Calculate the next available achievements
        $nextAvailableAchievements = $this->achievementService->getNextAvailableAchievements($user);

        // Get the current badge
        $currentBadge = $this->achievementService->getCurrentBadge($user);

        // Determine the next badge and remaining achievements for it
        [$nextBadge, $remainingToUnlockNextBadge] = $this->achievementService->getNextBadgeInfo($user);

        return response()->json([
            'unlocked_achievements' => $unlockedAchievements,
            'next_available_achievements' => $nextAvailableAchievements,
            'current_badge' => $currentBadge,
            'next_badge' => $nextBadge,
            'remaining_to_unlock_next_badge' => $remainingToUnlockNextBadge
        ]);
    }
}
