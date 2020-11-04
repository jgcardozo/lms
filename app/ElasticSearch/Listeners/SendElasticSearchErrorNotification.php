<?php

namespace App\ElasticSearch\Listeners;

use App\ElasticSearch\Events\ElasticSearchFailed;
use App\Mail\ESFailedMail;
use App\Role;
use Illuminate\Support\Facades\Mail;

class SendElasticSearchErrorNotification
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
     * @param ElasticSearchFailed $event
     * @return void
     */
    public function handle(ElasticSearchFailed $event)
    {
        // Implement role model
//        $admins = Role::where('name','admin')->first()->users()->get();
//        Mail::to($admins)->send(new ESFailedMail($event->exception, $event->action, $event->object));
    }
}
