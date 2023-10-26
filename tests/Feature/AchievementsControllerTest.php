<?php

use App\Achiever\Achievements\FiftyLessonsWatched;
use App\Achiever\Achievements\FirstCommentWritten;
use App\Achiever\Achievements\FirstLessonWatched;
use App\Achiever\Achievements\FiveCommentsWritten;
use App\Achiever\Achievements\FiveLessonsWatched;
use App\Achiever\Achievements\TenCommentsWritten;
use App\Achiever\Achievements\TenLessonsWatched;
use App\Achiever\Achievements\ThreeCommentsWritten;
use App\Achiever\Achievements\TwentyCommentsWritten;
use App\Achiever\Achievements\TwentyFiveLessonsWatched;
use App\Achiever\Badges\AdvancedBadge;
use App\Achiever\Badges\BeginnerBadge;
use App\Achiever\Badges\IntermediateBadge;
use App\Achiever\Badges\MasterBadge;
use App\Events\AchievementUnlocked;
use App\Events\CommentWritten;
use App\Events\LessonWatched;
use App\Models\Comment;
use App\Models\Lesson;
use App\Models\User;
use Illuminate\Support\Facades\Event;

it('provides accurate achievement and badge status for a user', function ($comments, $lessons, $unlocks, $nextAchievements, $remainingToNextBadge, $currentBadge, $nextBadge) {
    $mehran = User::factory()->create();

    Comment::factory($comments)
        ->for($mehran)
        ->create()
        ->each(fn ($comment) => event(new CommentWritten($comment)));

    Lesson::factory($lessons)
        ->create()
        ->each(function (Lesson $lesson) use ($mehran) {
            $lesson->watch($mehran);

            event(new LessonWatched($lesson, $mehran));
        });

    $res = $this->get(route('users.achievements.index', $mehran))
        ->assertOk();

    // Assert all the properties get filled correctly...
    $res->assertJson([
        'unlocked_achievements' => array_map(fn($unlock) => app($unlock)->name(), $unlocks)
    ]);

    $res->assertJson([
        'next_available_achievements' => array_map(fn($next) => app($next)->name(), $nextAchievements)
    ]);

    $res->assertJson([
        'current_badge' => app($currentBadge)->name(),
        'next_badge' => is_null($nextBadge) ? 0 : app($nextBadge)->name(),
        'remaining_to_unlock_next_badge' => $remainingToNextBadge,
    ]);
})
    ->with([
        [
            'comments' => 0,
            'lessons' => 0,
            'unlocked_achievements' => [],
            'next_achievements' => [
                FirstCommentWritten::class,
                FirstLessonWatched::class,
            ],
            'remaining' => 4,
            'current_badge' => BeginnerBadge::class,
            'next_badge' => IntermediateBadge::class,
        ],
        [
            'comments' => 3,
            'lessons' => 5,
            'unlocked_achievements' => [
                FirstCommentWritten::class,
                ThreeCommentsWritten::class,
                FirstLessonWatched::class,
                FiveLessonsWatched::class,
            ],
            'next_achievements' => [
                FiveCommentsWritten::class,
                TenLessonsWatched::class,
            ],
            'remaining' => 4,
            'current_badge' => IntermediateBadge::class,
            'next_badge' => AdvancedBadge::class,
        ],
        [
            'comments' => 20,
            'lessons' => 50,
            'unlocked_achievements' => [
                FirstCommentWritten::class,
                ThreeCommentsWritten::class,
                FiveCommentsWritten::class,
                TenCommentsWritten::class,
                TwentyCommentsWritten::class,
                FirstLessonWatched::class,
                FiveLessonsWatched::class,
                TenLessonsWatched::class,
                TwentyFiveLessonsWatched::class,
                FiftyLessonsWatched::class,
            ],
            'next_achievements' => [],
            'remaining' => 0,
            'current_badge' => MasterBadge::class,
            'next_badge' => null,
        ],
    ]);
