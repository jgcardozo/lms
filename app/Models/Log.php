<?php

namespace App\Models;

use App\ElasticSearch\Traits\HasElasticData;
use App\Http\Resources\Admin\ESLogResource;
use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    use HasElasticData;


    public function subject()
    {
        return $this->morphTo();
    }

    public function action()
    {
        return $this->belongsTo(Action::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function activity()
    {
        return $this->belongsTo(Activity::class);
    }

    public function toElasticSearchArray()
    {
        return new ESLogResource($this);
    }

    public static function elasticSearchMappings()
    {
        return [
            "properties" => [
                "id" => [ "type" => "integer" ],
                "user" => [
                    "type" => "object",
                    "properties" => [
                        "id" => [ "type" => "integer" ],
                        "type" => [ "type" => "keyword" ],
                        "name" => [ "type" => "keyword" ],
                        "email" => [ "type" => "keyword" ]
                    ]
                ],
                "cohorts" => [
                    "type" => "nested",
                    "properties" => [
                        "id" => [ "type" => "integer" ],
                        "name" => [ "type" => "keyword" ]
                    ]
                ],
                "subject" => [
                    "type" => "object",
                    "properties" => [
                        "type" => [ "type" => "keyword" ],
                        "id" => [ "type" => "integer"],
                        "tree" => [ "type" => "keyword" ],
                    ]
                ],
                "activity" => [
                    "type" => "object",
                    "properties" => [
                        "id" => [ "type" => "integer" ],
                        "name" => [ "type" => "keyword" ]
                    ]
                ],
                "action" => [
                    "type" => "object",
                    "properties" => [
                        "id" => [ "type" => "integer" ],
                        "name" => [ "type" => "keyword" ]
                    ]
                ],
                "created_at" => [
                    "type" => "date",
                    "format" => "yyyy-MM-dd HH:mm:ss"
                ]
            ]
        ];
    }
}
