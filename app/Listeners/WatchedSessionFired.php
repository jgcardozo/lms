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

        mixPanel()->track('Session completed', [
            'session_id' => $session->id,
            'session' => $session->title,
            'course' => $session->course ? $session->course->title : '',
            'module' => $session->lesson && $session->lesson->module ? $session->lesson->module->title : '',
            'lesson' => $session->lesson ? $session->lesson->title : ''
        ]);

        // Log the activity
        activity('session-watched')->causedBy(Auth::user())->withProperties(['ip' => request()->ip()])->performedOn($session)->log('User <strong>:causer.email</strong> finished session <strong>:subject.title</strong> [:subject.id]');
    }
}
