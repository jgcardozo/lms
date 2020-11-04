<?php


namespace App\ElasticSearch\Traits;


trait HasElasticData
{
    public function getModelData()
    {
        if(method_exists($this, 'toElasticSearchArray')) {
            return $this->toElasticSearchArray();
        }

        return $this->toArray();
    }

    public function getModelMappings()
    {
        if(method_exists($this, "elasticSearchMappings")) {
            return $this->elasticSearchMappings();
        }

        return new \stdClass();
    }
}
