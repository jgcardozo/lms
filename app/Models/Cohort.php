<?php

namespace App\Models;

use App\Traits\RecordActivity;
use Backpack\CRUD\CrudTrait;
use Illuminate\Database\Eloquent\Model;

class Cohort extends Model
{

    use CrudTrait;
    use RecordActivity;

    protected $fillable = [
        'name',
        'course_id',
        'schedule_id'
    ];

    public function fbLinks()
    {
        return $this->belongsToMany(Lesson::class,'easter_egg_links')->withPivot(['lesson_id','cohort_id','fb_link']);
    }

    public function logs()
    {
        return $this->morphMany('App\Models\Log', 'subject');
    }

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function schedule()
    {
        return $this->belongsTo(Schedule::class);
    }

    public function link($lessonId)
    {
        return $this->fbLinks()->find($lessonId)->pivot->fb_link ?? '';
    }
}