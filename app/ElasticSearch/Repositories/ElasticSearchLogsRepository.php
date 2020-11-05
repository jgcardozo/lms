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

    public function search(Request $request)
    {
        $parameters = [
            "index" => $this->indexName,
        ];

        $parameters['body'] = [
            "size" => 10000
        ];

        $this->setSortOrder($request->get('sort'), $request->get('order'), $parameters);

        $this->appendUserFilter($request->get('user_id'), $parameters);

        // if user is admin
        if($request->has('_token')) {
            $this->appendCauserFilter($request->get('causer'), $parameters); // admin or user

            $this->appendCohortFilter($request->get('cohort'), $parameters);

            $this->appendActionFilter($request->get('action'), $parameters);

            $this->appendActivityFilter($request->get('activity'), $parameters);

            $this->appendDateTimeRangeFilter($request->only(['fromDate', 'toDate']), $parameters);
        }

        try {
            return $this->client->search($parameters);
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

    private function appendDateTimeRangeFilter($dateRange, &$parameters)
    {
        $from = array_key_exists('fromDate', $dateRange) ? date("Y-m-d H:i:s", strtotime($dateRange['fromDate'])) : null;
        $to = array_key_exists('toDate', $dateRange) ? date("Y-m-d H:i:s", strtotime($dateRange['toDate'])) : null;

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
