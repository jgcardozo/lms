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
}