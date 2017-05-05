<?php

namespace App\Listeners;

use DB;
use Carbon\Carbon;
use App\Streaks\Streak;
use Illuminate\Auth\Events\Login;
use App\Streaks\Types\LoginStreak;
use App\Notifications\UnlockedByTag;
use Illuminate\Support\Facades\Log;
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
        /**
         * Log user login in 'user_logins' table. If there is a
         * record, create it, If no, update the date field.
         */
        $logIpValues = [
            'user_id' => $event->user->id,
            'ip' => request()->ip()
        ];

        $logIP = (array) DB::table('user_logins')->where($logIpValues)->first();
        if(empty($logIP))
        {
            $logIpValues['created_at'] = Carbon::now();
            DB::table('user_logins')->insert($logIpValues);
        }else{
            $logIP['created_at'] = Carbon::now();
            DB::table('user_logins')->where(array_except($logIP, ['created_at']))->update($logIP);
        }

		// Sync Infusionsoft user tags
        $is = new InfusionsoftController($event->user);
        $newTags = $is->sync();

        // Log the new tags
        if(!empty($newTags))
        {
            Log::info('User tags updated. | ID: ' . implode(', ', $newTags));
        }

        // Check for unlocked course/module/lesson/session
        // and notify the user
        /*
        $items = $is->checkUnlockedCourses($newTags);
        if(!empty($is))
        {
			foreach($items as $item)
			{
				$event->user->notify(new UnlockedByTag($item));
			}
        }
        */

		// Catch the login streak
		Streak::log(new LoginStreak());

		// Log the activity
		activity('user-logged')->causedBy($event->user)->withProperties(['ip' => request()->ip()])->log('User :causer.email has logged in.');
    }
}
