<?php

namespace App\Http\Controllers\Admin;

use App\Models\Cohort;
use App\Models\NotificationLog;
use App\Models\User;
use App\Models\Course;
use App\Notifications\CustomMessage;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use function MongoDB\BSON\toJSON;

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
		$logs = NotificationLog::with('user')->orderBy('created_at','DESC')->get();

		return view('lms.admin.notify')->with('courses', $courses)->with('cohorts', $cohorts)->with('users', $users)->with('logs',$logs);
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
        $uuid = (string) Str::uuid();

        $notifyLog = new NotificationLog();
        $notifyLog->user_id = Auth::id();
        $notifyLog->uuid = $uuid;
        $notifyLog->message = $message;

		if($radioButton === "all") {
            User::all()->each->notify(new CustomMessage($message,$uuid));

            $notifyLog->subject = ["type" => "All users"];
            $notifyLog->save();

            return redirect()->back();
        }

		if(!empty($specificUsers)) {
		    User::find($specificUsers)->each->notify(new CustomMessage($message,$uuid));

            $notifyLog->subject = ["users" => User::find($specificUsers), "type" => "specificUsers"];
            $notifyLog->save();

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

            User::find($usersToNotify)->each->notify(new CustomMessage($message,$uuid));


        } else {
            User::find($usersByCourses)->each->notify(new CustomMessage($message,$uuid));
            User::find($usersByCohort)->each->notify(new CustomMessage($message,$uuid));
        }

        $notifyLog->subject = ['cohorts' => Cohort::find($cohorts),'courses' => $courses, "type" => "cohortCourse"];
        $notifyLog->save();

		return redirect()->back();
	}
}
