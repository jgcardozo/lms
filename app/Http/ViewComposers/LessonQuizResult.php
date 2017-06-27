<?php

namespace App\Http\ViewComposers;

use DB;
use Auth;
use Illuminate\View\View;
use App\Models\LessonQuestion;

class LessonQuizResult
{
	/**
	 * Bind data to the view.
	 *
	 * @param  View  $view
	 * @return void
	 */
	public function compose(View $view)
	{
		$course = null;

		if(!empty($view->course))
		{
			$course = $view->course;
		}else if(!empty($view->module))
		{
			$course = $view->module->course;
		}else if($view->lesson)
		{
			$course = $view->lesson->course;
		}

		if($course)
		{
			$row = DB::table('class_marker_results')->where('user_id', Auth::user()->id)->where('course_id', $course->id)->get()->first();
			if($row && $row->passed)
			{
				$assessment_id = $row->assessment_id;
				$q = LessonQuestion::where('assessment_id', $assessment_id)->first();
				if($q)
				{
					$view->with('lessonQuizResultPage', $q->lesson);
				}
			}
		}
	}
}