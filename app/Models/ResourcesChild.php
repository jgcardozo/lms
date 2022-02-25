<?php

namespace App\Models;

use App\Models\ResourcesBank;
use App\Scopes\OrderScope;
use App\Traits\BackpackCrudTrait;
use Backpack\CRUD\CrudTrait;
use Cviebrock\EloquentSluggable\Sluggable;
use Cviebrock\EloquentSluggable\SluggableScopeHelpers;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ResourcesChild extends Model
{
    use Sluggable;
    use CrudTrait;
    use BackpackCrudTrait;
    use SluggableScopeHelpers;

    protected $fillable = [
        'title', 'slug', 'content', 'published',
    ];

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        if (\Route::currentRouteName() == 'crud.resourcesitems.reorder') {
            if (request()->has('resourcebank')) {
                $bank = ResourcesBank::find(request()->get('resourcebank'));
                $child = $bank->resourcesChildren->pluck('id')->toArray();
                static::addGlobalScope('byResourcebank', function (Builder $builder) use ($child) {
                    $builder->whereIn('id', $child);
                });
            }
        }

        static::addGlobalScope(new OrderScope);
    }

    public function sluggable()
    {
        return [
            'slug' => [
                'source' => 'title',
            ],
        ];
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function resourcesBank()
    {
        return $this->belongsToMany('App\Models\ResourcesBank', 'resourcechild_resourcebank',  'child_id', 'bank_id');
    }

} //class
