<?php

use App\Achiever\Achievements\FirstCommentWritten;
use App\Achiever\Achievements\FiveCommentsWritten;
use App\Achiever\Achievements\TenCommentsWritten;
use App\Achiever\Achievements\ThreeCommentsWritten;
use App\Achiever\Achievements\TwentyCommentsWritten;
use App\Events\AchievementUnlocked;
use App\Events\CommentWritten;
use App\Models\Comment;
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
