<?php


namespace App\ElasticSearch\Repositories;


use App\ElasticSearch\Events\ElasticSearchFailed;
use App\ElasticSearch\Interfaces\ElasticSearchRepositoryInterface;
use Elasticsearch\ClientBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class ElasticSearchLogsRepository implements ElasticSearchRepositoryInterface
{
    private $indexName;

    protected $client;

    public function __construct()
    {
        $this->client = ClientBuilder::create()->build();
        $this->indexName = config('elasticsearch.index.prefix') . "logs";
    }

    public function updateOrInsert(Model $model)
    {
        $parameters = [
            "index" => $this->indexName,
            "id" => $model->id,
            "refresh" => true,
            "retry_on_conflict" => 5,
            "body" => [
                "doc" => $model->getModelData(),
                "doc_as_upsert" => true
            ]
        ];

        try {
            if(!$this->client->exists($parameters)) {
                throw new \Exception("[ElasticSearchLogsRepository - updateOrInsert] Index {$this->indexName} does not exist.");
            }
            $this->client->update($parameters);
        } catch (\Exception $exception) {
            ElasticSearchFailed::dispatch($exception, 'insert', $model->id);
        }
    }

    public function delete(Model $model)
    {
        $parameters = [
            "index" => $this->indexName,
            "id" => $model->id
        ];

        try {
            $this->client->delete($parameters);
        } catch (\Exception $exception) {
            ElasticSearchFailed::dispatch($exception, 'delete', $model->id);
        }
    }

    public function insert(Model $model)
    {
        $parameters = [
            "index" => $this->indexName,
            "id" => $model->id,
            "body" => $model->getModelData()
        ];

        try {
            $this->client->index($parameters);
        } catch (\Exception $exception) {
            ElasticSearchFailed::dispatch($exception, 'index', $model->id);
        }

    }

    public function update(Model $model)
    {
        $parameters = [
            "index" => $this->indexName,
            "id" => $model->id,
            "body" => [
                "doc" => $model->getModelData()
            ]
        ];

        try {
            $this->client->update($parameters);
        } catch (\Exception $exception) {
            ElasticSearchFailed::dispatch($exception, 'update', $model->id);
        }

    }

    private function count($parameters)
    {
        if(array_key_exists("size", $parameters["body"])) {
            unset($parameters["body"]["size"]);
        }

        if(array_key_exists("sort", $parameters["body"])) {
            unset($parameters["body"]["sort"]);
        }

        return $this->client->count($parameters);
    }

    public function search(Request $request)
    {
        $filters = $request->get('filters');

        $parameters = [
            "index" => $this->indexName,
        ];

        $parameters['body'] = [
            "size" => 10000
        ];

        $this->setSortOrder($filters['sort'], $filters['order'], $parameters);

        $this->appendUserFilter($filters['user_id'], $parameters);

        $this->appendActionFilter($filters['action'], $parameters);

        $this->appendActivityFilter($filters['activity'], $parameters);

        $this->appendDateTimeRangeFilter($filters['fromDate'], $filters['toDate'], $parameters);

        // if user is admin
        if(is_null($filters['user_id'])) {
            $this->appendCauserFilter($filters['causer'], $parameters);

            $this->appendCohortFilter($filters['cohort'], $parameters);
        }

        try {
//            dd($parameters);
            $result = $this->client->search($parameters);
            $totalCount = $this->count($parameters);
            $result["hits"]["total"]["total"] = $totalCount["count"];

            return $result;
        } catch (\Exception $exception) {
            ElasticSearchFailed::dispatch($exception, 'search');
        }
    }

    private function setSortOrder($sort, $order, &$parameters)
    {
        $sort = $this->__getSortField($sort);
        $order = !is_null($order) ? $order : "desc";

        $parameters['body']['sort'] = [
            $sort => ["order" => $order]
        ];
    }

    private function __getSortField($sort) {
        switch ($sort) {
            case "id":
                return "id";
            case "user":
                return "user.email";
            case "action":
                return "action.name";
            case "subject":
                return "subject.tree";
            default:
                return "created_at";
        }
    }

    private function appendUserFilter($userId, &$parameters)
    {
        if (is_null($userId)) { return; }

        $parameters['body']['query']['bool']['filter'][] = [
            "term" => ["user.id" => (int)$userId],
        ];
    }

    private function appendCauserFilter($causer, &$parameters)
    {
        if (is_null($causer) || $causer == 'all') { return; }

        $parameters['body']['query']['bool']['filter'][] = [
            "term" => ["user.type" => $causer],
        ];
    }

    private function appendCohortFilter($cohort, &$parameters)
    {
        if (is_null($cohort) || $cohort == 'all') { return; }

        $parameters['body']['query']['bool']['must'][] = [
            "nested" => [
                "path" => "cohorts",
                "query" => [
                    "bool" => [
                        "must" => [
                            [
                                "match" => [ "cohorts.id" => (int)$cohort ]
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }

    private function appendActionFilter($action, &$parameters)
    {
        if (is_null($action) || $action == 'all') { return; }

        $parameters['body']['query']['bool']['filter'][] = [
            "term" => ["action.id" => (int)$action],
        ];
    }

    private function appendActivityFilter($activity, &$parameters)
    {
        if (is_null($activity) || $activity == 'all') { return; }

        $parameters['body']['query']['bool']['filter'][] = [
            "term" => ["activity.id" => (int)$activity],
        ];
    }

    private function appendDateTimeRangeFilter($fromDate, $toDate, &$parameters)
    {
        $from = !is_null($fromDate) ? date("Y-m-d H:i:s", strtotime($fromDate)) : null;
        $to = !is_null($toDate) ? date("Y-m-d H:i:s", strtotime($toDate)) : null;

        if($from) {
            $parameters['body']['query']['bool']['filter'][] = [
                "range" => [
                    "created_at" => [
                        "gte" => $from,
                    ]
                ]
            ];
        }

        if($to) {
            $parameters['body']['query']['bool']['filter'][] = [
                "range" => [
                    "created_at" => [
                        "lte" => $to,
                    ]
                ]
            ];
        }
    }
}
