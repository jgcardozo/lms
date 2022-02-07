<?php

namespace App\Models;

use App\Traits\ISLock;
use App\Traits\RecordActivity;
use Backpack\CRUD\CrudTrait;
use App\Traits\BackpackCrudTrait;
use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;
use Cviebrock\EloquentSluggable\SluggableScopeHelpers;

class ResourcesBank extends Model
{

    use ISLock;
    use Sluggable;
    use CrudTrait;
    use BackpackCrudTrait;
    use SluggableScopeHelpers;
    use RecordActivity;

    protected $fillable = [
        'title', 'slug', 'description', 'content', 'video_url', 'video_type_id', 'featured_image', 'header_image', 'sidebar_content', 'published'
    ];



     public function sluggable()
    {
        return [
            'slug' => [
                'source' => 'title'
            ]
        ];
    }

    public function getRouteKeyName()
    {
        return 'slug';
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
        $destination_path = 'resourcesbank/';

        $request = \Request::instance();
        $file = $request->file($attribute_name);
        $filename = date('mdYHis') . '_' . $file->getClientOriginalName();

        // Make the image
        $image = \Image::make($file);

        // Store the image on disk
        \Storage::disk($disk)->put($destination_path . $filename, $image->stream()->__toString());

        // Save the path to the database
        $this->attributes[$attribute_name] = $destination_path . $filename;
    }

    public function setHeaderImageAttribute($value)
    {
        $attribute_name = 'header_image';
        $disk = 's3';
        $destination_path = 'resourcesbank/header';

        $request = \Request::instance();
        $file = $request->file($attribute_name);
        $filename = date('mdYHis') . '_' . $file->getClientOriginalName();

        // Make the image
        $image = \Image::make($file);

        // Store the image on disk
        \Storage::disk($disk)->put($destination_path . $filename, $image->stream()->__toString());

        // Save the path to the database
        $this->attributes[$attribute_name] = $destination_path . $filename;
    }

    /**
     * Get image from S3
     */
    public function getFeaturedImageUrlAttribute()
    {
        return !empty($this->featured_image) ? 'https://s3-us-west-1.amazonaws.com/ask-lms/' . rawurlencode($this->featured_image) : '';
    }

    /**
     * Get image from S3
     */
    public function getHeaderImageUrlAttribute()
    {
        return !empty($this->header_image) ? 'https://s3-us-west-1.amazonaws.com/ask-lms/' . rawurlencode($this->header_image) : '';
    }

    public function video_type()
    {
        return $this->belongsTo('App\Models\VideoType');
    }

    /**
     * Check if course is locked
     *
     * @return bool
     */
    public function getIsLockedAttribute()
    {
        return $this->is_tag_locked() && !is_role_admin();
    }


    public function logs()
    {
        return $this->morphMany('App\Models\Log', 'subject');
    }


    public function resources_children()
    {
        return $this->belongsToMany('App\Models\ResourcesChild', 'rcontainer_rsection', 'container_id', 'section_id')->withPivot('created_at');
    }


}
