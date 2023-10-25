<?php

use App\Achiever\Badges\AdvancedBadge;
use App\Achiever\Badges\BeginnerBadge;
use App\Achiever\Badges\IntermediateBadge;
use App\Achiever\Badges\MasterBadge;
use App\Events\BadgeUnlocked;
use App\Events\CommentWritten;
use App\Events\LessonWatched;
use App\Models\Comment;
use App\Models\Lesson;
use App\Models\User;
use Illuminate\Support\Facades\Event;

it('correctly awards and tracks user badges based on unlocked achievements', function ($lessonsCount, $commentsCount, $badgeClass, $badgesCount) {
    Event::fake(BadgeUnlocked::class);

    $mehran = User::factory()->create();

    Lesson::factory($lessonsCount)
        ->create()
        ->each(function (Lesson $lesson) use ($mehran) {
            $lesson->watch($mehran);

            event(new LessonWatched($lesson, $mehran));
        });

    Comment::factory($commentsCount)
        ->for($mehran)
        ->create()
        ->each(fn ($comment) => event(new CommentWritten($comment)));

    expect($mehran->badges)
        ->toHaveCount($badgesCount)
        ->and($mehran->badges->contains('name', app($badgeClass)->name()))
        ->toBeTrue();

    Event::assertDispatched(BadgeUnlocked::class, function ($event) use ($badgeClass) {
        return $event->badge_name == app($badgeClass)->name();
    });
})
    ->with([
        [
            'lessons_count' => 0,
            'comments_count' => 0,
            'badge_class' => BeginnerBadge::class,
            'badges_count' => 1,
        ],
        [
            'lessons_count' => 25,
            'comments_count' => 0,
            'badge_class' => IntermediateBadge::class,
            'badges_count' => 2,
        ],
        [
            'lessons_count' => 50,
            'comments_count' => 5,
            'badge_class' => AdvancedBadge::class,
            'badges_count' => 3,
        ],
        [
            'lessons_count' => 50,
            'comments_count' => 20,
            'badge_class' => MasterBadge::class,
            'badges_count' => 4,
        ],
    ]);
