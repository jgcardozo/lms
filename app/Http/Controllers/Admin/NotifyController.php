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
		$users = User::all();

		return view('lms.admin.notify')->with('courses', $courses)->with('cohorts', $cohorts)->with('users', $users);
	}

	public function notify(Request $request)
	{

        $usersByCohort = [];
        $usersByCourses = [];
		$users = User::select('*');

		$courses = $request->input('courses');
		$cohorts = $request->input('cohorts');
		$message = $request->input('message');
		$specificUsers = $request->input('users');
		$radioButton = $request->input('optradio');

		if($radioButton === "all") {
            User::all()->each->notify(new CustomMessage($message));

            return redirect()->back();
        }

		if(!empty($specificUsers)) {
		    User::find($specificUsers)->each->notify(new CustomMessage($message));

            return redirect()->back();
        }

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

			$usersByCourses = $users->pluck('id')->toArray();
		}

		if(!empty($cohorts))
        {
            foreach ($cohorts as $cohort) {
                $users = Cohort::find($cohort)->users;
                $users = $users->pluck('id')->toArray();
                $usersByCohort = array_merge($usersByCohort,$users);
            }
        }

        if(!empty($usersByCourses) && !empty($usersByCohort)) {

            $usersToNotify = array_merge($usersByCohort,$usersByCourses);
            $usersToNotify = array_unique($usersToNotify);

            User::find($usersToNotify)->each->notify(new CustomMessage($message));
        } else {
            User::find($usersByCourses)->each->notify(new CustomMessage($message));
            User::find($usersByCohort)->each->notify(new CustomMessage($message));
        }

		return redirect()->back();
	}
}
