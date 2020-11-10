<?php


namespace App\ElasticSearch\Interfaces;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

interface ElasticSearchRepositoryInterface
{
    public function updateOrInsert(Model $model);

    public function delete(Model $model);

    public function insert(Model $model);

    public function update(Model $model);

    public function search(Request $request);

}
