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
		View::composer(['lms.courses.*', 'lms.modules.*', 'lms.lessons.*', 'lms.coachingcalls.*', 'lms.user.*', 'lms.calendar.*'], 'App\Http\ViewComposers\CancelPaymentAlertsComposer');
		View::composer(['lms.courses.single', 'lms.courses.starter', 'lms.modules.*', 'lms.lessons.*', 'lms.coachingcalls.*'], 'App\Http\ViewComposers\PaymentAlertsComposer');
		View::composer(['lms.lessons.single'], 'App\Http\ViewComposers\LessonCongratulation');
		View::composer(['lms.courses.*', 'lms.modules.*', 'lms.lessons.single'], 'App\Http\ViewComposers\LessonQuizResult');
		View::composer(['lms.admin.schedule_create','lms.admin.schedule_edit'],'App\Http\ViewComposers\ScheduleCreateComposer');
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
