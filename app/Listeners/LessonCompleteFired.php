<?php

namespace App\Listeners;

use App\Events\LessonComplete;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class LessonCompleteFired
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
     * @param  LessonComplete  $event
     * @return void
     */
    public function handle(LessonComplete $event)
    {
        $lesson = $event->lesson;

        mixPanel()->track('Lesson completed', [
            'lesson_id' => $lesson->id,
            'lesson' => $lesson->title,
            'course' => $lesson->course ? $lesson->course->title : '',
            'module' => $lesson->module ? $lesson->module->title : ''
        ]);
    }
}
