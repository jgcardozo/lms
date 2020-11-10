<?php

namespace App\ElasticSearch\Commands;

use App\ElasticSearch\ElasticSearch;
use App\ElasticSearch\Traits\HasElasticData;
use Illuminate\Console\Command;

class ElasticsearchIndexCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'elasticsearch:index {model : The model that need to be indexed. Ex "App\User"}';

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
               $this->error("Index for model ".class_basename($class). " exists. Run elasticsearch:reindex '{$model}' instead.");
            } else {
                $elastic->createIndex();
                $elastic->index();
            }

            $this->info("Indexing done.");

        } else {
            $this->error("The class {$model} does not exist.");
        }
    }
}
