<?php

namespace App\Listeners;

use App\Events\StarterVideosCompleted;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class StarterVideosCompletedFired
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
     * @param  StarterVideosCompleted  $event
     * @return void
     */
    public function handle(StarterVideosCompleted $event)
    {
        $course = $event->course;

        mixPanel()->track('Intro videos completed', [
            'course_id' => $course->id,
            'course' => $course->title
        ]);
    }
}
