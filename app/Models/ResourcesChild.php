<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Backpack\CRUD\CrudTrait;
use App\Traits\BackpackCrudTrait;
use Cviebrock\EloquentSluggable\Sluggable;
use Cviebrock\EloquentSluggable\SluggableScopeHelpers;

class ResourcesChild extends Model
{
	use Sluggable;
    use CrudTrait;
    use BackpackCrudTrait;
    use SluggableScopeHelpers;
	
	
    protected $fillable = [
        'title', 'slug', 'content',  'published'
    ];
	
	
	public function sluggable()
    {
        return [
            'slug' => [
                'source' => 'title'
            ]
        ];
    }
	
	public function getRouteKeyName()
    {
        return 'slug';
    }


    public function resources_bank()
    {
        return $this->belongsToMany('App\Models\ResourcesBank', 'rcontainer_rsection', 'container_id', 'section_id')->withPivot('created_at');
    }
	
} //class
