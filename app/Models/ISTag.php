<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ISTag extends Model
{
    public $timestamps = false;

    protected $table = 'is_tags';

    public function lockables()
    {
		return $this->morphedByMany('App\Models\Course', 'lockable', 'is_lockables', 'tag_id', 'lockable_id');
    }

    public function users()
    {
        return $this->belongsToMany('App\Models\User', 'tag_user', 'tag_id', 'user_id');
    }
}
