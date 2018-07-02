<?php

namespace App\Listeners;

use InfusionsoftFlow;
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
        $user = \Auth::user();

        if(!$lesson->questions->isEmpty())
        {
            InfusionsoftFlow::addTag($user->contact_id, 7224);
        }

        mixPanel()->track('Lesson completed', [
            'lesson_id' => $lesson->id,
            'lesson' => $lesson->title,
            'course' => $lesson->course ? $lesson->course->title : '',
            'module' => $lesson->module ? $lesson->module->title : ''
        ]);

        $p = new \App\Models\Progress;
        $p->user()->associate($user);
        $p->setCreatedAt(now());
        $p->save();
        $lesson->progress()->save($p);

        $log = new \App\Models\Log;
        $log->user_id = $user->id;
        $log->action_id = 2;
        $log->save();
        $lesson->logs()->save($log);
    }
}
