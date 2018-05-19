<?php

namespace App\Traits;


use App\Models\Log;

trait RecordActivity
{
    protected static function bootRecordActivity()
    {
        static::created(function($model){
            $log = new Log;
            $log->user_id = \Auth::user()->id;
            $log->action_id = 14;
            $log->activity_id = 7;
            $log->save();

            $model->logs()->save($log);
        });

        static::updated(function($model){
            $log = new Log;
            $log->user_id = \Auth::user()->id;
            $log->action_id = 8;
            $log->activity_id = 7;
            $log->save();

            $model->logs()->save($log);
        });

        static::deleted(function($model){
            $log = new Log;
            $log->user_id = \Auth::user()->id;
            $log->action_id = 13;
            $log->activity_id = 7;
            $log->save();

            $model->logs()->save($log);
        });
    }
}