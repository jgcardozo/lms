<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    protected $fillable = [
        'name',
        'course_id',
        'schedule_type'
    ];


    public function cohorts()
    {
        return $this->hasMany(Cohort::class);
    }

}
