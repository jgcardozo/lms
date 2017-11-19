<?php

namespace App\Models;

use Backpack\CRUD\CrudTrait;
use Illuminate\Database\Eloquent\Model;

class ResourceTag extends Model
{

    use CrudTrait;

    protected $fillable = ['title'];

    public $timestamps = false;

    public function resources()
    {
        return $this->belongsToMany(Resource::class);
    }
}
