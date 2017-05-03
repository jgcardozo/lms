<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ComposerServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
		View::composer(['lms.courses.*', 'lms.modules.*', 'lms.lessons.*', 'lms.coachingcalls.*'], 'App\Http\ViewComposers\HeaderComposer');
		View::composer(['*'], 'App\Http\ViewComposers\NotificationsComposer');

		View::composer(['lms.courses.single', 'lms.courses.starter', 'lms.modules.*', 'lms.lessons.*', 'lms.coachingcalls.*'], 'App\Http\ViewComposers\PaymentAlertsComposer');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
