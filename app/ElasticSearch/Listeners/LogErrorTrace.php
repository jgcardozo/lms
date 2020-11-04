<?php

namespace App\ElasticSearch\Listeners;

use App\ElasticSearch\Events\ElasticSearchFailed;

class LogErrorTrace
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
     * @param  object  $event
     * @return void
     */
    public function handle(ElasticSearchFailed $event)
    {
        \Log::critical($event->exception->getMessage());
        \Log::critical('Exception: ' . get_class($event->exception));
        \Log::critical($event->exception->getTraceAsString());
    }
}
