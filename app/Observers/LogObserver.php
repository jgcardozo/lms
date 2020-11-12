<?php


namespace App\Observers;


use App\ElasticSearch\Repositories\ElasticSearchLogsRepository;
use App\Models\Log;

class LogObserver
{
    protected $repo;

    public function __construct()
    {
        $this->repo = new ElasticSearchLogsRepository();
    }


    /**
     * Handle the order "deleted" event.
     *
     * @param Log $log
     * @return void
     */
    public function saved(Log $log)
    {
        $this->repo->updateOrInsert($log);
    }

}