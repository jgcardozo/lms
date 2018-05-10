<?php

namespace App\Listeners;

use App\Events\ModuleComplete;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class ModuleCompleteFired
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
     * @param  ModuleComplete  $event
     * @return void
     */
    public function handle(ModuleComplete $event)
    {
        $module = $event->module;

        mixPanel()->track('Module completed', [
            'module_id' => $module->id,
            'module' => $module->title,
            'course' => $module->course ? $module->course->title : ''
        ]);

        $log = new \App\Models\Log;
        $log->user_id = \Auth::user()->id;
        $log->action_id = 2;
        $log->save();
        $module->logs()->save($log);
    }
}
