<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ISCourseProductId extends Model
{
    protected $table = 'is_course_products';
	protected $primaryKey = 'course_id';
	protected $guarded = 'course_id';

	public function course()
	{
		return $this->belongsTo('App\Models\Course');
	}
}
