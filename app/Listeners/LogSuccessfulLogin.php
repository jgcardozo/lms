<?php

namespace App\Listeners;

use App\Streaks\Streak;
use Illuminate\Auth\Events\Login;
use App\Streaks\Types\LoginStreak;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Http\Controllers\InfusionsoftController;

class LogSuccessfulLogin
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  Login  $event
     * @return void
     */
    public function handle(Login $event)
    {
		// Sync Infusionsoft user tags
        $is = new InfusionsoftController($event->user);
        $is->sync();

		// Catch the streak
		Streak::log(new LoginStreak());
    }
}
