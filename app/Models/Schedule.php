<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Backpack\CRUD\CrudTrait;

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

    public function admin_course_link()
    {
        ?>
        <a href="<?php echo route('crud.course.edit', [$this->course_id]); ?>">
            <?php echo Course::find($this->course_id)->title ?>
        </a>
        <?php
    }

}
