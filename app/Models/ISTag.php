<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ISTag extends Model
{
    public $timestamps = false;

    protected $table = 'is_tags';

    public function users()
    {
        return $this->belongsToMany('App\Models\User', 'tag_user', 'tag_id', 'user_id');
    }
}
