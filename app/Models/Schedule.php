<?php

namespace App\Models;

use Carbon\Carbon;
use Backpack\CRUD\CrudTrait;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    use CrudTrait;

    protected $fillable = [
        'name',
        'course_id',
        'schedule_type'
    ];


    public function logs()
    {
        return $this->morphMany('App\Models\Log', 'subject');
    }

    public function cohorts()
    {
        return $this->hasMany(Cohort::class);
    }

    public function modules()
    {
        return $this->morphedByMany(Module::class,'schedulable');
    }

    public function lessons()
    {
        return $this->morphedByMany(Lesson::class,'schedulable');
    }

    public function sessions()
    {
        return $this->morphedByMany(Session::class,'schedulable');
    }

    public function resources() // juan sept18
    {
        return $this->morphedByMany(Resource::class, 'schedulable');
    }

    public function admin_course_link()
    {
        ?>
        <a href="<?php echo route('crud.course.edit', [$this->course_id]); ?>">
            <?php echo Course::find($this->course_id)->title ?>
        </a>
        <?php
    }

    public function getDayZeroAttribute($value)
    {
        return Carbon::parse($value)->format('m/d/Y g:i A');   
    }


}//class
