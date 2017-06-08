<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
	protected $primaryKey = 'user_id';
	protected $guarded = 'user_id';

	protected $fillable = [
		'first_name', 'last_name', 'phone1', 'company'
	];

    public function user()
	{
		return $this->belongsTo('App\Models\User');
	}
}
