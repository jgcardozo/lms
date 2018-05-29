<?php

namespace App\Traits;


use App\Models\Log;
use Illuminate\Support\Facades\Artisan;

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

            Artisan::call('cache:clear');
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

            $logs = $model->logs;

            foreach ($logs as $logg) {
                if(empty($model->title)) {
                    $logg->deleted = $model->name;
                } else {
                    $logg->deleted = $model->title;
                }

                $logg->save();
            }
        });
    }
}