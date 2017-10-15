<?php

namespace App\Http\Controllers;

use App\Events\StarterVideosCompleted;
use Auth;
use App\Models\Session;
use Illuminate\Http\Request;
use App\Events\WatchedSession;
use App\Events\LessonComplete;
use App\Events\ModuleComplete;

class SessionController extends Controller
{
	public function __construct()
	{
		$this->middleware('onlyajax');
	}

	/**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    public function complete($id)
    {
		$session = Session::find($id);

		if(!$session) {
			abort(404);
		}

		$finished = [
		    'lesson_complete' => false,
            'module_complete' => false,
            'course_complete' => false,
            'session' => $session,
            'lesson' => $session->lesson ? $session->lesson : '',
            'module' => $session->lesson && $session->lesson->module ? $session->lesson->module : '',
            'course' => $session->lesson && $session->lesson->module && $session->lesson->module->course ? $session->lesson->module->course : ''
        ];

		event(new WatchedSession($session));

		if($session->lesson && $session->lesson->is_completed)
		{
			$lesson = $session->lesson;

			event(new LessonComplete($lesson));

			$finished['lesson_complete'] = true;

			if($lesson->module && $lesson->module->is_completed)
			{
				$module = $lesson->module;

				event(new ModuleComplete($module));

                $finished['module_complete'] = true;

                if($module->course && $module->course->is_completed)
                {
                    $finished['course_complete'] = true;
                }
			}
		}

		if($session->starter_course_id && $session->course->areAllStarterSeen())
		{
			event(new StarterVideosCompleted($session->course));
		}

        return response()->json($finished);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
		$user_id = Auth::user()->id;
		$session = Session::findOrFail($id);
		$key = 'session_' . $id . '_' . $user_id;
		$videoProgress = session($key, 0);

		return view('lms.courses.session-popup')->with('session', $session)->with('videoprogress', $videoProgress);
    }

	/**
	 * Store user watched video progress in session.
	 * Session name session_$session_id_$user_id. Ex. session_9_2
	 *
	 * @param $id
	 * @param Request $request
	 */
	public function videoprogress($id, Request $request)
	{
		$user_id = Auth::user()->id;
		$key = 'session_' . $id . '_' . $user_id;
		$_progress = $request->get('progress');

		$value = session($key, 0);
		$progress = $_progress + $value;
		if($progress > 100)
		{
			$progress = 100;
		}

		session([$key => $progress]);
		session()->save();
	}

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
