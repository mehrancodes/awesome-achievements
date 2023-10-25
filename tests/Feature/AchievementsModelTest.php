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
use App\Events\AchievementUnlocked;
use App\Events\CommentWritten;
use App\Events\LessonWatched;
use App\Models\Comment;
use App\Models\Lesson;
use App\Models\User;
use Illuminate\Support\Facades\Event;

it('correctly unlocks and tracks all "Comment Written" achievements incrementally for a user', function ($comments, $achievements) {
    Event::fake(AchievementUnlocked::class);

    $mehran = User::factory()->create();

    Comment::factory($comments)
        ->for($mehran)
        ->create()
        ->each(fn ($comment) => event(new CommentWritten($comment)));

    $expect = expect($mehran->refresh()->achievements->pluck('name')->toArray());

    if (is_null($achievements)) {
        $expect->toBeEmpty();

        Event::assertNothingDispatched();
    } else {
        $expect->toContain(app($achievements)->name());

        Event::assertDispatched(AchievementUnlocked::class, function ($event) use ($achievements) {
            return $event->achievement_name == app($achievements)->name();
        });
    }
})
    ->with([
        [
            'comments' => 0,
            'achievement' => null,
        ],
        [
            'comments' => 1,
            'achievement' => FirstCommentWritten::class,
        ],
        [
            'comments' => 3,
            'achievement' => ThreeCommentsWritten::class,
        ],
        [
            'comments' => 5,
            'achievement' => FiveCommentsWritten::class,
        ],
        [
            'comments' => 10,
            'achievement' => TenCommentsWritten::class,
        ],
        [
            'comments' => 20,
            'achievement' => TwentyCommentsWritten::class,
        ],
    ]);

it('correctly unlocks and tracks all "Lesson Watched" achievements incrementally for a user', function ($lessons, $achievements) {
    Event::fake(AchievementUnlocked::class);

    $mehran = User::factory()->create();

    Lesson::factory($lessons)
        ->create()
        ->each(function (Lesson $lesson) use ($mehran) {
            $lesson->watch($mehran);

            event(new LessonWatched($lesson, $mehran));
        });

    $expect = expect($mehran->refresh()->achievements->pluck('name')->toArray());

    if (is_null($achievements)) {
        $expect->toBeEmpty();

        Event::assertNothingDispatched();
    } else {
        $expect->toContain(app($achievements)->name());

        Event::assertDispatched(AchievementUnlocked::class, function ($event) use ($achievements) {
            return $event->achievement_name == app($achievements)->name();
        });
    }
})
    ->with([
        [
            'lessons' => 0,
            'achievement' => null,
        ],
        [
            'lessons' => 1,
            'achievement' => FirstLessonWatched::class,
        ],
        [
            'lessons' => 5,
            'achievement' => FiveLessonsWatched::class,
        ],
        [
            'lessons' => 10,
            'achievement' => TenLessonsWatched::class,
        ],
        [
            'lessons' => 25,
            'achievement' => TwentyFiveLessonsWatched::class,
        ],
        [
            'lessons' => 50,
            'achievement' => FiftyLessonsWatched::class,
        ],
    ]);
