<?php

namespace App\Listeners;

use App\Events\CommentWritten;
use App\Models\User;

class SyncCommentsAchievements
{
    public function handle(CommentWritten $event)
    {
        /** @var User $user */
        $user = $event->comment->user;

        $user->syncAchievements();

        $user->syncBadges();
    }
}
