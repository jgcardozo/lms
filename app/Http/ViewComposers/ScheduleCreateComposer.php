<?php
/**
 * Created by PhpStorm.
 * User: Ivan
 * Date: 27-Apr-18
 * Time: 10:16
 */

namespace App\Http\ViewComposers;

use App\Models\Cohort;
use Illuminate\View\View;
use App\Models\Course;

class ScheduleCreateComposer
{
    protected $courses,$cohorts;


    public function __construct()
    {
        $this->courses = Course::with('modules.lessons')->get();
        $this->cohorts = Cohort::with('course')->get();
    }

    public function compose(View $view)
    {
        $view->with('courses',$this->courses)->with('cohorts',$this->cohorts);
    }
}