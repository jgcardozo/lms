<?php

namespace App\Listeners;

use Auth;
use App\Events\WatchedSession;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class WatchedSessionFired
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
     * @param  WatchedSession  $event
     * @return void
     */
    public function handle(WatchedSession $event)
    {
        $session = $event->session;

        // Complete video for user
        $session->markAsComplete();

        // Log the activity
        activity('session-watched')->causedBy(Auth::user())->withProperties(['ip' => request()->ip()])->performedOn($session)->log('User <strong>:causer.email</strong> finished session <strong>:subject.title</strong> [:subject.id]');
    }
}
