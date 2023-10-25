<?php

namespace App\Achiever\Achievements;

use App\Enums\AchievementsTypeEnum;
use App\Models\User;

class FirstLessonWatched extends AchievementType
{
    public function qualifier(User $user): bool
    {
        return $user->watched()->count() >= 1;
    }

    public function name(): string
    {
        return 'First Lesson Watched';
    }

    public function type(): AchievementsTypeEnum
    {
        return AchievementsTypeEnum::LESSON;
    }

    public function order(): int
    {
        return 5;
    }
}

