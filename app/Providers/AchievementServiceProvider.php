<?php

namespace App\Providers;

use App\Services\AchievementService;
use Illuminate\Support\ServiceProvider;

class AchievementServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(AchievementService::class, function ($app) {
            return new AchievementService(
                config('achievements.lessons_watched'),
                config('achievements.comments_written')
            );
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
