<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
	protected $primaryKey = 'user_id';
	protected $guarded = 'user_id';

    public function user() {
		return $this->belongsTo('App\Models\User');
	}
}
