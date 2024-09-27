<?php

namespace Innoboxrr\GoogleCalendar\Providers;

use Illuminate\Support\ServiceProvider;
use Innoboxrr\GoogleCalendar\Services\GoogleCalendarService;

class AppServiceProvider extends ServiceProvider
{

    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../../config/google-calendar.php', 'google-calendar');
        $this->app->singleton(GoogleCalendarService::class, function (): GoogleCalendarService {
            return new GoogleCalendarService();
        });
    }

    public function boot()
    {
        // $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');
        // $this->loadViewsFrom(__DIR__.'/../../resources/views', 'innoboxrrgooglecalendar');
        if ($this->app->runningInConsole()) {
            // $this->publishes([__DIR__.'/../../resources/views' => resource_path('views/vendor/innoboxrrgooglecalendar'),], 'views');
            $this->publishes([__DIR__.'/../../config/google-calendar.php' => config_path('google-calendar.php')], 'config');
        }
    }
}