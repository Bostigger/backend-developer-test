<?php

namespace Tests\Feature;

use App\Models\Comment;
use App\Models\Lesson;
use App\Models\User;
use App\Services\AchievementService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;


class AchievementFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_application_returns_a_successful_response(): void
    {
        $user = User::factory()->create();

        $response = $this->get("/users/{$user->id}/achievements");

        $response->assertStatus(200);
    }

    public function test_user_can_retrieve_achievements_and_badges()
    {
        $user = User::factory()->create();

        // Simulate user watching lessons to unlock some achievements
        $this->simulateWatchingLessons($user, 5);

        // Simulate user writing comments  to unlock some achievements
        $this->simulateWritingComments($user, 3);

        // Get expected outcomes based on the current application logic
        $unlockedAchievements = app(AchievementService::class)->getUnlockedAchievements($user);
        $nextAvailableAchievements = app(AchievementService::class)->getNextAvailableAchievements($user);
        $currentBadge = app(AchievementService::class)->getCurrentBadge($user);
        $nextBadgeInfo = app(AchievementService::class)->getNextBadgeInfo($user);

        // Make a GET request to the achievements endpoint
        $response = $this->actingAs($user)->get('/users/' . $user->id . '/achievements');

        // Assert the response
        $response->assertStatus(200)
            ->assertJson([
                'unlocked_achievements' => $unlockedAchievements,
                'next_available_achievements' => $nextAvailableAchievements,
                'current_badge' => $currentBadge,
                'next_badge' => $nextBadgeInfo[0],
                'remaining_to_unlock_next_badge' => $nextBadgeInfo[1]
            ]);
    }

    public function test_new_user_with_no_achievements_or_badges()
    {
        $newUser = User::factory()->create();

        $response = $this->actingAs($newUser)->get('/users/' . $newUser->id . '/achievements');

        $response->assertStatus(200)
            ->assertJson([
                'unlocked_achievements' => [],
                'next_available_achievements' => ['First Lesson Watched', 'First Comment Written'],
                'current_badge' => 'Beginner',
                'next_badge' => 'Intermediate',
                'remaining_to_unlock_next_badge' => 4
            ]);
    }

    public function test_user_on_verge_of_unlocking_new_achievement()
    {
        $user = User::factory()->create();

        // Just before unlocking 5 Lessons Watched achievement
        $this->simulateWatchingLessons($user, 4);

        $response = $this->actingAs($user)->get('/users/' . $user->id . '/achievements');

        $response->assertStatus(200)
            ->assertJson([
                'unlocked_achievements' => ['First Lesson Watched'],
                'next_available_achievements' => ['5 Lessons Watched'],
                'current_badge' => 'Beginner',
                'next_badge' => 'Intermediate',
                'remaining_to_unlock_next_badge' => 3
            ]);
    }


    public function test_request_for_nonexistent_user()
    {
        $user = User::factory()->create();

        $nonExistentUserId = 999; // Assuming this ID does not exist
        $response = $this->actingAs($user)->get('/users/' . $nonExistentUserId . '/achievements');

        $response->assertStatus(404);
    }


    private function simulateWatchingLessons($user, $count)
    {
        $lessons = Lesson::factory()->count($count)->create();
        foreach ($lessons as $lesson) {
            $user->lessons()->attach($lesson->id, ['watched' => true]);
        }
    }

    private function simulateWritingComments($user, $count)
    {
        Comment::factory()->count($count)->create(['user_id' => $user->id]);
    }
}
