<?php

namespace App\Models;

use App\Models\Session;
use App\Traits\RecordActivity;
use Illuminate\Http\File;
use App\Scopes\OrderScope;
use Backpack\CRUD\CrudTrait;
use App\Traits\BackpackCrudTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Resource extends Model
{
	use CrudTrait, BackpackCrudTrait;
	use RecordActivity;

	protected $fillable = ['title', 'file_url'];

	/**
	 * The "booting" method of the model.
	 *
	 * @return void
	 */
	protected static function boot()
	{
		parent::boot();

		if(\Route::currentRouteName() == 'crud.resource.reorder')
		{
			if(request()->has('session'))
			{
				$session = Session::find(request()->get('session'));
				$resources = $session->resources->pluck('id')->toArray();
				static::addGlobalScope('bySession', function (Builder $builder) use ($resources) {
					$builder->whereIn('id', $resources);
				});
			}
		}

		static::addGlobalScope(new OrderScope);
	}

	public function getFileAttribute()
	{
		return !empty($this->file_url) ? 'https://s3-us-west-1.amazonaws.com/ask-lms/' . $this->file_url : '';
	}

	public function getFileExtensionAttribute()
	{
		return pathinfo($this->file_url, PATHINFO_EXTENSION);
	}

	public function getShortFilenameAttribute($len = 8)
	{
		$filename = pathinfo($this->file_url, PATHINFO_FILENAME);

		if(strlen($filename) > $len)
		{
			return substr($filename, 0, 8) . '&hellip;' . $this->file_extension;
		}

		return $filename . '.' . $this->file_extension;
	}

	public function getFileSizeMbAttribute()
	{
		return human_filesize($this->file_size);
	}

	/*
	|--------------------------------------------------------------------------
	| Relations
	|--------------------------------------------------------------------------
	*/
    public function logs()
    {
        return $this->morphMany('App\Models\Log', 'subject');
    }

    public function sessions()
	{
		return $this->belongsToMany('App\Models\Session', 'resource_session');
	}

	public function resourceTags()
    {
        return $this->belongsToMany(ResourceTag::class);
    }

	/*
	|--------------------------------------------------------------------------
	| Mutators
	|--------------------------------------------------------------------------
	*/
	public function setFileUrlAttribute($value)
	{
		$attribute_name = 'file_url';
		$disk = 's3';
		$destination_path = 'resources/';

		$request = \Request::instance();
		$file = $request->file($attribute_name);
		$filename = date('mdYHis') . '_' . $file->getClientOriginalName();
		$filesize = !empty($file->getClientSize()) ? $file->getClientSize() : 0;

		\Storage::disk($disk)->put($destination_path . $filename, file_get_contents($file));
		$this->attributes[$attribute_name] = $destination_path . $filename;
		$this->attributes['file_size'] = $filesize;
	}
}
