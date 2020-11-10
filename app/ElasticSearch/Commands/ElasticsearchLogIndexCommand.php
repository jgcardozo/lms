<?php

namespace App\ElasticSearch\Commands;

use App\ElasticSearch\ElasticSearch;
use App\ElasticSearch\Traits\HasElasticData;
use App\Models\Log;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;

class ElasticsearchLogIndexCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'elasticsearch:batch-index {model : The model that need to be indexed. Ex "App\User"}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Index the given model into ElasticSearch';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $model = $this->argument('model');

        // In order to call the boot method on CoachingCall we need a logged in user, and it has to be an admin
        $admin = Log::where('activity_id', 7)->whereNull('deleted_user')->first()->user;
        Auth::loginUsingId($admin->id);

        if(class_exists($model)) {
            $class = new $model;

            if(!in_array(HasElasticData::class, class_uses_recursive($class))) {
                $this->error('Trait "HasElasticData" missing on model."');
                return;
            }

            $this->info('Starting to index model '.class_basename($class));


            $elastic = new ElasticSearch();

            $elastic->setModel($model);
            $elastic->setIndexName();

            if($elastic->indexExists()) {
                $this->error("Index for model ".class_basename($class). " exists. Run elasticsearch:batch-reindex '{$model}' instead.");
            } else {
                $elastic->createIndex();

                $this->info("Index created");
                // calculate and initialize progress bar
                $progressBarLimit = $class::select('id')->orderBy('id','desc')->limit(1)->first()->id;

                $progressBar = $this->output->createProgressBar($progressBarLimit);
                $progressBar->start();

                $elastic->batchIndexModel($progressBar);

                $progressBar->finish();
            }

            $this->info("Indexing done.");

        } else {
            $this->error("The class {$model} does not exist.");
        }

        Auth::logout();
    }
}
