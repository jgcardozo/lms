<?php


namespace App\ElasticSearch;

use Elasticsearch\ClientBuilder;
use Illuminate\Support\Str;

class ElasticSearch
{
    private $client, $model, $indexName;

    public function __construct()
    {
        $this->client = ClientBuilder::create()->build();
    }

    public function setModel($model)
    {
        $this->model = $model;
    }

    public function setIndexName($value = null)
    {
        if(isset($value)) {
            $this->indexName = config('elasticsearch.index.prefix').$value;
        } else {
            $this->indexName = config('elasticsearch.index.prefix').Str::plural(strtolower(class_basename($this->model)));
        }
    }

    public function indexExists()
    {
        $parameters = [
            "index" => $this->indexName
        ];

        return $this->client->indices()->exists($parameters);
    }

    public function createIndex()
    {
        $class = new $this->model;

        $parameters = [
            "index" => $this->indexName
        ];

        $additionalBodyParameters = [
            'settings' => [
                'number_of_shards' => 5,
                'number_of_replicas' => 1,
                'refresh_interval' => '1s'
            ],
            'mappings' => $class->getModelMappings()
        ];

        $parameters["body"] = $additionalBodyParameters;

        $this->client->indices()->create($parameters);
    }

    public function index()
    {
        $class = new $this->model;

        $bulk['body'] = [];

        foreach ($class::all() as $entity) {
            $bulk['body'][] = [
                "index" => [
                    "_index" => $this->indexName,
                    "_type" => "_doc",
                    "_id" => $entity->id,
                ]
            ];

            $bulk['body'][] = $entity->getModelData();
        }

        $this->client->bulk($bulk);
    }

    public function reindex()
    {
        $parameters = [
            "index" => $this->indexName
        ];

        $this->client->indices()->delete($parameters);

        $this->createIndex();

        $this->index();
    }

    public function batchIndexModel($progressBar)
    {
        try {
            $chunk = 2000;  // how many model entries should be indexed per batch

            $class = new $this->model;

            $startingId = 0;
            $endingId = $class::select('id')->orderBy('id','desc')->limit(1)->first()->id;

            $bulk['body'] = [];

            $i = 0;

            while (true) {
                $logs = $class::where('id', '>=', $startingId + $i * $chunk)
                    ->where('id', '<', $startingId + ($i * $chunk + $chunk))
                    ->get();

                foreach ($logs as $log) {
                    $bulk['body'][] = [
                        "index" => [
                            "_index" => $this->indexName,
                            "_type" => "_doc",
                            "_id" => $log->id,
                        ]
                    ];

                    $bulk['body'][] = $log->getModelData();
                }

                if (empty($bulk['body'])) {
                    return;
                }

                $this->client->bulk($bulk);
                $bulk['body'] = [];

                $progressBar->advance($logs->count());

                if (($startingId + $i * $chunk + $chunk) >= $endingId) {
                    break;
                }

                $i++;
            }
        } catch (\Exception $e) {
            \Log::info([
               "message" => $e->getMessage(),
               "traceString" => $e->getTraceAsString(),
               "trace" => $e->getTrace(),
               "file" =>$e->getFile(),
               "line" => $e->getLine()
            ]);
        }
    }

    public function batchReindexModel($progressBar)
    {
        $parameters = [
            "index" => $this->indexName
        ];

        $this->client->indices()->delete($parameters);

        $this->createIndex();

        $this->batchIndexModel($progressBar);
    }
}
