<?php

namespace App\ElasticSearch\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ElasticSearchFailed
{
    use Dispatchable, SerializesModels;

    public $object;
    public $exception;
    public $action;

    /**
     * Create a new event instance.
     *
     * @param $object
     * @param $exception
     * @param $action
     */
    public function __construct($exception, $action, $object=null)
    {
        $this->exception = $exception;
        $this->action = $action;
        $this->object = $object;
    }
}
