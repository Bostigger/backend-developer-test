<?php

namespace Tests\Unit;

use App\Events\AchievementUnlocked;
use App\Events\BadgeUnlocked;
use App\Events\CommentWritten;
use App\Events\LessonWatched;
use App\Listeners\HandleCommentWritten;
use App\Listeners\HandleLessonWatched;
use App\Models\Comment;
use App\Models\Lesson;
use App\Services\AchievementService;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AchievementTest extends TestCase
{
    use RefreshDatabase;

    private $achievementService;

    protected function setUp(): void
    {
        parent::setUp();

        // Resolve the AchievementService and store it in a property
        $this->achievementService = app(AchievementService::class);
    }


    public function test_it_should_return_no_unlocked_achievements_for_a_new_user()
    {
        $user = User::factory()->create();

        // Get unlocked achievements
        $unlockedAchievements = $this->achievementService->getUnlockedAchievements($user);

        $this->assertEmpty($unlockedAchievements);
    }

    public function test_it_unlocks_first_lesson_watched_achievement()
    {
        $user = User::factory()->create();

        // Simulate the user watching 1 lesson
        $this->simulateWatchingLessons($user, 1);

        // Get unlocked achievements
        $unlockedAchievements = $this->achievementService->getUnlockedAchievements($user);

        // Assert: Check the 'First Lesson Watched' achievement is unlocked
        $this->assertContains('First Lesson Watched', $unlockedAchievements);
    }


    public function test_it_unlocks_five_lessons_watched_achievement()
    {
        $user = User::factory()->create();

        // Simulate the user watching five lessons
        $this->simulateWatchingLessons($user, 5);


        // Get unlocked achievements
        $unlockedAchievements = $this->achievementService->getUnlockedAchievements($user);

        // Check the '5 Lessons Watched' achievement is unlocked
        $this->assertContains('5 Lessons Watched', $unlockedAchievements);
    }


    public function test_it_unlocks_ten_lessons_watched_achievement()
    {
        $user = User::factory()->create();

        // Simulate the user watching ten lessons
        $this->simulateWatchingLessons($user, 10);

        // Get unlocked achievements
        $unlockedAchievements = $this->achievementService->getUnlockedAchievements($user);

        // Check the '10 Lessons Watched' achievement is unlocked
        $this->assertContains('10 Lessons Watched', $unlockedAchievements);
    }


    public function test_it_unlocks_twenty_five_lessons_watched_achievement()
    {
        $user = User::factory()->create();

        // Simulate the user watching twenty-five lessons
        $this->simulateWatchingLessons($user, 25);

        // Get unlocked achievements
        $unlockedAchievements = $this->achievementService->getUnlockedAchievements($user);

        // Check the '25 Lessons Watched' achievement is unlocked
        $this->assertContains('25 Lessons Watched', $unlockedAchievements);
    }


    public function test_it_unlocks_fifty_lessons_watched_achievement()
    {
        $user = User::factory()->create();

        // Simulate the user watching fifty lessons
        $this->simulateWatchingLessons($user, 50);

        // Get unlocked achievements
        $unlockedAchievements = $this->achievementService->getUnlockedAchievements($user);

        // Check the '50 Lessons Watched' achievement is unlocked
        $this->assertContains('50 Lessons Watched', $unlockedAchievements);
    }

    public function test_it_unlocks_first_comment_written_achievement()
    {
        $user = User::factory()->create();

        //simulate user writing 1 comments
        $this->simulateWritingComments($user, 1);

        // Get unlocked achievements
        $unlockedAchievements = $this->achievementService->getUnlockedAchievements($user);

        // Assert: Check the 'First Comment Written' achievement is unlocked
        $this->assertContains('First Comment Written', $unlockedAchievements);
    }

    public function test_it_unlocks_three_comment_written_achievement()
    {
        $user = User::factory()->create();

        //simulate user writing 3 comments
        $this->simulateWritingComments($user, 3);

        // Get unlocked achievements
        $unlockedAchievements = $this->achievementService->getUnlockedAchievements($user);

        // Assert: Check the '3 Comments Written' achievement is unlocked
        $this->assertContains('3 Comments Written', $unlockedAchievements);
    }

    public function test_it_unlocks_five_comment_written_achievement()
    {
        $user = User::factory()->create();

        //simulate user writing 5 comments
        $this->simulateWritingComments($user, 5);

        // Get unlocked achievements
        $unlockedAchievements = $this->achievementService->getUnlockedAchievements($user);

        // Check the '5 Comments Written' achievement is unlocked
        $this->assertContains('5 Comments Written', $unlockedAchievements);
    }

    public function test_it_unlocks_ten_comment_written_achievement()
    {
        $user = User::factory()->create();

        //simulate user writing 10 comments
        $this->simulateWritingComments($user, 10);

        // Get unlocked achievements
        $unlockedAchievements = $this->achievementService->getUnlockedAchievements($user);

        // Check the '10 Comments Written' achievement is unlocked
        $this->assertContains('10 Comments Written', $unlockedAchievements);
    }

    public function test_it_unlocks_twenty_comment_written_achievement()
    {

        $user = User::factory()->create();

        //simulate user writing 20 comments
        $this->simulateWritingComments($user, 20);

        // Get unlocked achievements
        $unlockedAchievements = $this->achievementService->getUnlockedAchievements($user);

        // Check the '20 Comments Written' achievement is unlocked
        $this->assertContains('20 Comments Written', $unlockedAchievements);
    }

    public function test_it_awards_beginner_badge_by_Default()
    {
        $user = User::factory()->create();

        // Get user current badge
        $currentBadge = $this->achievementService->getCurrentBadge($user);

        $this->assertEquals('Beginner', $currentBadge);

    }

    public function test_it_awards_intermediate_badge_for_four_achievements()
    {
        $user = User::factory()->create();

        // Simulate watching 2 lessons
        // This unlocks 1 achievement ; 'First Lesson Watched'
        $this->simulateWatchingLessons($user, 2);

        // Simulate writing 5 comments
        // This unlocks 3 achievements ; 'First Comment Written' ,'3 Comments Written','5 Comments Written'
        $this->simulateWritingComments($user, 5);

        $user->refresh(); // Refresh user

        //Get user current badge
        $currentBadge = $this->achievementService->getCurrentBadge($user);

        $this->assertEquals('Intermediate', $currentBadge);
    }


    public function test_it_awards_advanced_badge_for_eight_achievements()
    {
        $user = User::factory()->create();

        // Simulate watching 30 lessons
        $this->simulateWatchingLessons($user, 30);

        // Simulate writing 10 comments
        $this->simulateWritingComments($user, 10);

        $user->refresh(); // Refresh user

        //Get user current badge
        $currentBadge = $this->achievementService->getCurrentBadge($user);

        $this->assertEquals('Advanced', $currentBadge);
    }

    public function test_it_awards_master_badge_for_ten_achievements()
    {
        $user = User::factory()->create();

        // Simulate user watching 50 lessons
        $this->simulateWatchingLessons($user, 50);

        // Simulate user writing 20 comments
        $this->simulateWritingComments($user, 20);

        $user->refresh(); // Refresh user

        //Get user current badge
        $currentBadge = $this->achievementService->getCurrentBadge($user);

        $this->assertEquals('Master', $currentBadge);
    }


    public function test_it_unlocks_multiple_achievements_simultaneously()
    {
        $user = User::factory()->create();

        //simulate that user watching 11 lessons
        $this->simulateWatchingLessons($user, 11);

        // simulate that user writing 3 Comments
        $this->simulateWritingComments($user, 3);

        // Get unlocked achievements
        $unlockedAchievements = $this->achievementService->getUnlockedAchievements($user);

        $expectedAchievements = ['First Lesson Watched', '5 Lessons Watched', '10 Lessons Watched', 'First Comment Written', '3 Comments Written'];
        foreach ($expectedAchievements as $achievement) {
            $this->assertContains($achievement, $unlockedAchievements);
        }
    }


    public function test_it_does_not_unlock_achievements_more_than_once()
    {
        $user = User::factory()->create();

        //simulate that user watching 2 lessons
        $this->simulateWatchingLessons($user, 2);

        // Get unlocked achievements
        $unlockedAchievements = $this->achievementService->getUnlockedAchievements($user);

        $this->assertEquals(1, count(array_filter($unlockedAchievements, function ($achievement) {
            return $achievement == 'First Lesson Watched';
        })));
    }


    public function test_it_does_not_unlock_achievement_if_criteria_not_met()
    {
        $user = User::factory()->create();

        //simulate user watching 4 lessons, just before hitting the 5 Lessons Watched achievement
        $this->simulateWatchingLessons($user, 4);

        //simulate user writing 9 comments, just before hitting the 10 Comments Written achievement
        $this->simulateWritingComments($user, 9);

        // Get unlocked achievements
        $unlockedAchievements = $this->achievementService->getUnlockedAchievements($user);

        $this->assertNotContains('5 Lessons Watched', $unlockedAchievements);
        $this->assertNotContains('10 Comments Written', $unlockedAchievements);
    }

    public function test_it_correctly_calculates_next_available_achievements()
    {
        $user = User::factory()->create();

        // Simulate the user watching 5 lessons
        // This unlocks ''First Lesson Watched' and '5 Lessons Watched' achievements
        $this->simulateWatchingLessons($user, 5);

        // Simulate the user writing 2 comments
        // This unlocks 'First comment Written' achievements
        $this->simulateWritingComments($user, 2);

        // Refresh the user to update the state
        $user->refresh();

        // Get the next available achievements
        $nextAchievements = $this->achievementService->getNextAvailableAchievements($user);

        // Assert that the correct next achievements are identified
        $this->assertContains('10 Lessons Watched', $nextAchievements);
        $this->assertContains('3 Comments Written', $nextAchievements);
    }


    public function test_it_correctly_calculates_the_number_of_additional_achievements_the_user_must_unlock_to_earn_next_badge()
    {
        $user = User::factory()->create();

        // Simulate the user watching 10 lessons
        // Unlocks 3 achievements; 'First Lesson Watched' , '5 Lessons Watched' , '10 Lessons Watched'
        $this->simulateWatchingLessons($user, 10);

        // Simulate the user writing 3 lessons
        // Unlocks 2 achievements; 'First Comment Written' , '3 Comments Written'
        $this->simulateWritingComments($user, 3);

        //Get next badge info
        $nextBadgeInfo = $this->achievementService->getNextBadgeInfo($user);

        //structure of this $nextBadgeInfo is; {"nextBadgeInfo":["next achievement_name","additional required achievements to get next achievement"]}

        $this->assertEquals(3, $nextBadgeInfo[1]);
        $this->assertEquals('Advanced', $nextBadgeInfo[0]);

    }


    public function test_it_calculates_achievements_correctly_for_different_users()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        // Simulate achievements for user1 - Watch 5 lessons
        $this->simulateWatchingLessons($user1, 5);

        // Simulate achievements for user2 - Write 3 comments
        $this->simulateWritingComments($user2, 3);

        // Refresh users to update their states
        $user1->refresh();
        $user2->refresh();

        // Get the unlocked achievements for each user
        $achievementsUser1 = $this->achievementService->getUnlockedAchievements($user1);
        $achievementsUser2 = $this->achievementService->getUnlockedAchievements($user2);

        // Assert that the achievements for each user are different
        $this->assertNotEquals($achievementsUser1, $achievementsUser2);
    }


    public function test_it_handles_invalid_data_gracefully()
    {
        $user = new User();

        // Get the unlocked achievements
        $achievements = $this->achievementService->getUnlockedAchievements($user);

        $this->assertEmpty($achievements);
    }


    public function test_it_correctly_transitions_from_beginner_to_intermediate_badge()
    {
        $user = User::factory()->create();

        //simulate the user watching 5 lessons
        // Unlocks 'First Lesson Watched' and '5 Lessons Watched' // 2 achievements earned
        $this->simulateWatchingLessons($user, 5);


        //simulate the user writing 3 Comments
        // Unlocks 'First Comment Written' and '3 Comments Written' // 2 achievements earned = 4 achievements in total
        $this->simulateWritingComments($user, 3);

        $user->refresh();

        //Get the use current Badge
        $currentBadge = $this->achievementService->getCurrentBadge($user);

        $this->assertEquals('Intermediate', $currentBadge);
    }


    public function test_it_updates_achievements_and_badge_simultaneously()
    {
        $user = User::factory()->create();

        // Simulate user watching 25 lessons this should unlock an achievement that also updates the badge
        $this->simulateWatchingLessons($user, 25);

        $user->refresh();

        // Get the unlocked achievements
        $achievements = $this->achievementService->getUnlockedAchievements($user);

        // Get the unlocked badge
        $currentBadge = $this->achievementService->getCurrentBadge($user);

        $this->assertContains('25 Lessons Watched', $achievements);
        $this->assertEquals('Intermediate', $currentBadge);
    }


    public function test_it_unlocks_overlapping_achievements_correctly()
    {
        $user = User::factory()->create();

        //simulate the user watching 5 lessons
        //  This should unlock both 'First Lesson Watched' and '5 Lessons Watched'
        $this->simulateWatchingLessons($user, 5);

        // Get the unlocked achievements
        $achievements = $this->achievementService->getUnlockedAchievements($user);

        $this->assertContains('First Lesson Watched', $achievements);
        $this->assertContains('5 Lessons Watched', $achievements);
    }


    public function test_handle_comment_written_listener_triggers_expected_events()
    {
        Event::fake([
            AchievementUnlocked::class,
            BadgeUnlocked::class,
        ]);

        $user = User::factory()->create();
        $comment = Comment::factory()->create(['user_id' => $user->id]);
        $listener = new HandleCommentWritten($this->achievementService);

        $listener->handle(new CommentWritten($comment));

        Event::assertDispatched(AchievementUnlocked::class);
        Event::assertDispatched(BadgeUnlocked::class);
    }


    public function test_handle_lesson_watched_listener_triggers_expected_events()
    {
        Event::fake([
            AchievementUnlocked::class,
            BadgeUnlocked::class,
        ]);

        $user = User::factory()->create();
        $lesson = Lesson::factory()->create();

        $user->lessons()->attach($lesson->id, ['watched' => true]);

        $listener = new HandleLessonWatched($this->achievementService);

        $listener->handle(new LessonWatched($lesson, $user));

        Event::assertDispatched(AchievementUnlocked::class);
        Event::assertDispatched(BadgeUnlocked::class);
    }


    public function test_it_unlocks_achievements_and_updates_badge_when_a_comment_is_written()
    {
        $user = User::factory()->create();
        $comment = Comment::factory()->create(['user_id' => $user->id]);

        // Fake specific events
        Event::fake([
            AchievementUnlocked::class,
            BadgeUnlocked::class
        ]);

        // Manually dispatch the CommentWritten event
        CommentWritten::dispatch($comment);


        $user->refresh(); // Refresh the user

        // Get the unlocked achievements
        $achievements = $this->achievementService->getUnlockedAchievements($user);

        // Get the current user badge
        $badge = $this->achievementService->getCurrentBadge($user);

        $this->assertContains('First Comment Written', $achievements);
        $this->assertEquals('Beginner', $badge);

        Event::assertDispatched(AchievementUnlocked::class);
        Event::assertDispatched(BadgeUnlocked::class);
    }


    public function test_it_unlocks_achievements_and_updates_badge_when_a_lesson_is_watched()
    {

        $user = User::factory()->create();
        $lesson = Lesson::factory()->create();

        $user->lessons()->attach($lesson->id, ['watched' => true]);

        Event::fake([
            AchievementUnlocked::class,
            BadgeUnlocked::class
        ]);

        LessonWatched::dispatch($lesson, $user);

        // Get the unlocked achievements
        $achievements = $this->achievementService->getUnlockedAchievements($user);

        // Get the current badge
        $badge = $this->achievementService->getCurrentBadge($user);

        $this->assertContains('First Lesson Watched', $achievements);
        $this->assertEquals('Beginner', $badge);

        Event::assertDispatched(AchievementUnlocked::class);
        Event::assertDispatched(BadgeUnlocked::class);
    }


    public function test_it_dispatches_achievement_unlocked_event_with_correct_data()
    {

        $user = User::factory()->create();
        $lesson = Lesson::factory()->create();

        Event::fake([
            AchievementUnlocked::class,
        ]);

        $user->lessons()->attach($lesson->id, ['watched' => true]);
        LessonWatched::dispatch($lesson, $user);

        Event::assertDispatched(AchievementUnlocked::class, function ($event) use ($user) {
            return $event->achievement_name === 'First Lesson Watched' && $event->user->id === $user->id;
        });
    }


    public function test_it_does_not_dispatch_duplicate_achievement_unlocked_events()
    {

        $user = User::factory()->create();
        $lesson = Lesson::factory()->create();

        Event::fake([
            AchievementUnlocked::class,
        ]);

        // Watch the same lesson multiple times
        for ($i = 0; $i < 2; $i++) {
            $user->lessons()->attach($lesson->id, ['watched' => true]);
            LessonWatched::dispatch($lesson, $user);
        }

        // Assert that the AchievementUnlocked event is dispatched only once
        Event::assertDispatchedTimes(AchievementUnlocked::class, 1);
    }


    public function test_it_dispatches_events_correctly_for_different_users()
    {
        Event::fake([
            AchievementUnlocked::class
        ]);

        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $lesson = Lesson::factory()->create();

        $user1->lessons()->attach($lesson->id, ['watched' => true]);
        $user2->lessons()->attach($lesson->id, ['watched' => true]);

        LessonWatched::dispatch($lesson, $user1);
        LessonWatched::dispatch($lesson, $user2);

        // Assert that the events are dispatched for each user
        Event::assertDispatched(AchievementUnlocked::class, function ($event) use ($user1) {
            return $event->user->id === $user1->id;
        });
        Event::assertDispatched(AchievementUnlocked::class, function ($event) use ($user2) {
            return $event->user->id === $user2->id;
        });
    }


    public function test_it_does_not_dispatch_events_prematurely()
    {
        Event::fake([
            AchievementUnlocked::class
        ]);

        $user = User::factory()->create();

        // Just below the threshold for the next achievement
        $lessons = Lesson::factory()->count(4)->create();

        foreach ($lessons as $lesson) {
            $user->lessons()->attach($lesson->id, ['watched' => true]);
            LessonWatched::dispatch($lesson, $user);
        }

        // Assert that the AchievementUnlocked event is not dispatched
        Event::assertNotDispatched(AchievementUnlocked::class, function ($event) {
            return $event->achievement_name === '5 Lessons Watched';
        });
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
