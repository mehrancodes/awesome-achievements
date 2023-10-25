<?php

namespace App\Providers;

use App\Models\UserAchievement;
use App\Observers\UserAchievementObserver;
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
        UserAchievement::observe(UserAchievementObserver::class);
    }
}
