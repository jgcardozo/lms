<?php

namespace App\Http\Controllers\Admin;

use App\Models\Cohort;
use App\Models\User;
use App\Models\Course;
use App\Notifications\CustomMessage;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class NotifyController extends Controller
{
	public function __construct()
	{
		$this->middleware('role:Administrator');
	}

	public function index()
	{
		$courses = Course::get();
		$cohorts = Cohort::get();

		return view('lms.admin.notify')->with('courses', $courses)->with('cohorts', $cohorts);
	}

	public function notify(Request $request)
	{
		$users = User::select('*');

		$courses = $request->get('courses');
		$cohorts = $request->get('cohorts');
		$message = $request->get('message');

		if(!empty($courses))
		{
			$courses = Course::findMany($courses);
			$tags = [];
			foreach($courses as $course)
			{
				foreach($course->lock_tags as $lock_tag)
				{
					$tags[] = $lock_tag->id;
				}
			}

			$users->whereHas('is_tags', function($query) use ($tags) {
				$query->whereIn('id', $tags);
			});
		}

		if(!empty($cohorts))
        {
            $users->whereIn('cohort_id', $cohorts);
        }

		$users->get()->each->notify(new CustomMessage($message));

		return redirect()->back();
	}
}
