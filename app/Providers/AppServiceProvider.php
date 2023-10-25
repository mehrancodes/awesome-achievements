<?php

namespace App\Providers;

use App\Models\User;
use App\Models\UserAchievement;
use App\Models\UserBadge;
use App\Observers\UserAchievementObserver;
use App\Observers\UserBadgeObserver;
use App\Observers\UserObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        User::observe(UserObserver::class);
        UserAchievement::observe(UserAchievementObserver::class);
        UserBadge::observe(UserBadgeObserver::class);
    }
}
