<?php

namespace App\Models;

use App\Scopes\OrderScope;
use Backpack\CRUD\CrudTrait;
use App\Traits\BackpackCrudTrait;
use Illuminate\Database\Eloquent\Model;

class LessonQuestion extends Model
{
	use CrudTrait;
	use BackpackCrudTrait;

	protected $fillable = [
		'question', 'video_url', 'video_type_id', 'video_title', 'description', 'featured_image', 'outer_url', 'featured_image', 'assessment_embed_id', 'assessment_id', 'lesson_id', 'is_tags', 'assessment_pass_tags', 'assessment_fail_tags'
	];

	/**
	 * The "booting" method of the model.
	 *
	 * @return void
	 */
	protected static function boot()
	{
		parent::boot();

		static::addGlobalScope(new OrderScope);
	}

	/**
	 * Get image from S3
	 */
	public function getFeaturedImageUrlAttribute()
	{
		// TODO: Check why this is not working
		// $s3image = \Storage::disk('s3')->url($this->featured_image);

		return !empty($this->featured_image) ? 'https://s3-us-west-1.amazonaws.com/ask-lms/' . rawurlencode($this->featured_image) : '';
	}

	/*
	|--------------------------------------------------------------------------
	| Relations
	|--------------------------------------------------------------------------
	*/
	public function lesson()
	{
		return $this->belongsTo('App\Models\Lesson');
	}

	/*
	|--------------------------------------------------------------------------
	| Mutators
	|--------------------------------------------------------------------------
	*/
	public function setFeaturedImageAttribute($value)
	{
		$attribute_name = 'featured_image';
		$disk = 's3';
		$destination_path = 'questions/';

		$request = \Request::instance();
		$file = $request->file($attribute_name);
		$filename = date('mdYHis') . '_' . $file->getClientOriginalName();

		// Make the image
		$image = \Image::make($file);

		// Store the image on disk
		\Storage::disk($disk)->put($destination_path . $filename, $image->stream()->__toString(), 'public');

		// Save the path to the database
		$this->attributes[$attribute_name] = $destination_path . $filename;
	}

    public function video_type()
    {
        return $this->belongsTo('App\Models\VideoType');
    }

	/*
	|--------------------------------------------------------------------------
	| Backpack model callbacks
	|--------------------------------------------------------------------------
	*/
	public function admin_lesson_link()
	{
		if(!$this->lesson) return;

		ob_start();
		?>
		<a href="<?php echo route('crud.lesson.edit', [$this->lesson->id]); ?>">
			<?php echo $this->lesson->title ?>
		</a>
		<?php
		$button = ob_get_clean();
		return $button;
	}
}
