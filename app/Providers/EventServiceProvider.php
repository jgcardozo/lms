<?php

namespace App\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'App\Events\WatchedSession' => [
            'App\Listeners\WatchedSessionFired',
        ],

        // This applies when all the starter videos are finished within a course
        'App\Events\StarterVideosCompleted' => [
            'App\Listeners\StarterVideosCompletedFired'
        ],

        'App\Events\LessonComplete' => [
            'App\Listeners\LessonCompleteFired'
        ],

        'App\Events\ModuleComplete' => [
            'App\Listeners\ModuleCompleteFired'
        ],

        'Illuminate\Auth\Events\Login' => [
            'App\Listeners\LogSuccessfulLogin'
        ]
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
