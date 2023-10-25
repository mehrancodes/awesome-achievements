<?php

namespace App\Actions;

use App\Models\User;

class UserAchievementSyncer
{
    public function execute(User $user)
    {
        $user->syncAchievements();
    }
}
