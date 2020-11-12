##ElasticSearch Implementation Guide

###1. Indexing documents
Requirements:
- Use the `App\ElasticSearch\Traits\HasElasticData` on the model you want to index
- Run the `php artisan elasticsearch:index {modelName}` command and replace `modelName` with the name of the model 
you would like to index. Ex. `php artisan elasticsearch:index "App\Users"`
- Set the `ELASTICSEARCH_INDEX_PREFIX` variable in the `.env` to your choosing. This is used mostly because the 
staging and the production server are on the same branch so the indices won't overlap from both environments

Optional:
- If you want to reindex a certain model run `php artisan elasticsearch:reindex {modelName}`

###2. Setting Mappings
By default no mappings will be put in the index of the indexing model. If you want to set mapping for the model 
implement `public static function elasticSearchMappings()` into your model. This function **MUST** return an array!

Ex: 
```php
    class User extends Authenticatable
    {
        use HasElasticData;

        public static function elasticSearchMappings()
        {
            return [
                "properties" => [
                    "name" => [ "type" => "keyword" ],
                    "email" => [ "type" => "text" ].
                    ...
            ];
        }
    }
```

###3. Setting the indexed data
By default the models `toArray()` function will be used when indexing documents. If you want you can implement the 
`toElasticSearchArray()` on your model to structure your data differently. This function **MUST** return an array!
