<?php

namespace App\Models;

use Illuminate\Http\File;
use Backpack\CRUD\CrudTrait;
use App\Traits\BackpackCrudTrait;
use Illuminate\Database\Eloquent\Model;

class Resource extends Model
{
	use CrudTrait, BackpackCrudTrait;

	protected $fillable = ['title', 'file_url'];

	/*
	|--------------------------------------------------------------------------
	| Relations
	|--------------------------------------------------------------------------
	*/
    public function sessions()
	{
		return $this->belongsToMany('App\Models\Session', 'resource_session');
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

	public function getFileAttribute()
	{
		return !empty($this->file_url) ? 'https://s3-us-west-1.amazonaws.com/ask-lms/' . $this->file_url : '';
	}
}
