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
		$destination_path = '';

		$request = \Request::instance();
		$file = $request->file($attribute_name);
		$filename = date('mdYHis') . '_' . $file->getClientOriginalName();

		\Storage::disk($disk)->put($destination_path . $filename, file_get_contents($file));
		$this->attributes[$attribute_name] = $destination_path . $filename;
	}
}
