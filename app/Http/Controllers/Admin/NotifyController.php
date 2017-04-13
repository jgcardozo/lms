<?php

namespace App\Http\Controllers\Admin;

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

		return view('lms.admin.notify')->with('courses', $courses);
	}

	public function notify(Request $request)
	{
		$users = User::get();

		$courses = $request->get('courses');
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

			$users = User::whereHas('is_tags', function($query) use ($tags) {
				$query->whereIn('id', $tags);
			})->get();
		}else{
			$user = User::get();
		}

		$users->each->notify(new CustomMessage($message));

		return redirect()->back();
	}
}
