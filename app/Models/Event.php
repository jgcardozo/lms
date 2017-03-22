<?php

namespace App\Models;

use Backpack\CRUD\CrudTrait;
use App\Traits\BackpackCrudTrait;
use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;
use Cviebrock\EloquentSluggable\SluggableScopeHelpers;

class Event extends Model
{
	use CrudTrait;
	use Sluggable;
	use BackpackCrudTrait;
	use SluggableScopeHelpers;

	protected $fillable = ['title', 'short_description', 'description', 'start_date', 'end_date', 'event_image', 'course_id'];

	/*
	|--------------------------------------------------------------------------
	| Relations
	|--------------------------------------------------------------------------
	*/
	public function course()
	{
		return $this->belongsTo('App\Models\Course');
	}

	public function sluggable()
	{
		return [
			'slug' => [
				'source' => 'title'
			]
		];
	}

	/*
	|--------------------------------------------------------------------------
	| Backpack model callbacks
	|--------------------------------------------------------------------------
	*/
	public function admin_course_link()
	{
		if(!$this->course) return;

		ob_start();
		?>
		<a href="<?php echo route('crud.course.edit', [$this->course->id]); ?>">
			<?php echo $this->course->title ?>
		</a>
		<?php
		$button = ob_get_clean();
		return $button;
	}
}
