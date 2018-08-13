<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Auth;
use Carbon\Carbon;
use App\Models\User;
use InfusionsoftFlow;
use App\Models\Lesson;
use Illuminate\Http\Request;
use App\Models\LessonQuestion;
use Illuminate\Support\Facades\File;

class LessonController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($slug)
    {
        $lesson = Lesson::findBySlugOrFail($slug);

        if(!$lesson) {
            abort(404);
        }

        return view('lms.lessons.single')->with(['lesson' => $lesson]);
    }

    public function postToFb(Lesson $lesson)
    {

        if(!$lesson->usersPosted()->where('id',Auth::user()->id)->exists()) {
            $lesson->usersPosted()->attach(Auth::user()->id);

            mixPanel()->track('Posted to Facebook', [
                'lesson_id' => $lesson->id,
                'lesson' => $lesson->title,
                'course' => $lesson->course ? $lesson->course->title : '',
                'module' => $lesson->module ? $lesson->module->title : ''
            ]);

            $log = new \App\Models\Log;
            $log->user_id = Auth::user()->id;
            $log->action_id = 6;
            $log->activity_id = 1;
            $log->save();
        }

		return redirect()->back();
    }

	public function assessmentResult($slug)
	{
		$lesson = Lesson::findBySlugOrFail($slug);

		if(!$lesson)
		{
			abort(404);
		}

		$user = Auth::user()->id;
		$score = null;
		$pass = null;

		if($lesson->q_answered)
		{
			$row = DB::table('class_marker_results')->where('user_id', $user)->where('assessment_id', $lesson->q_answered->assessment_id)->first();
			$score = $row && $row->passed ? $row->score : null;
		}

		return view('lms.lessons.result')->with(['lesson' => $lesson, 'score' => $score, 'result' => $row]);
	}

    public function answerQuestion(Lesson $lesson)
	{
		$qID = request()->get('question');
		$user = Auth::user();

		$lesson->userAnswered()->attach([$user->id => ['question_id' => $qID]]);
		$video = LessonQuestion::find($qID);

		if(!empty($video->is_tags))
		{
			InfusionsoftFlow::addTag($user->contact_id, explode(',', $video->is_tags));
		}

		return response()->json([
			'status' => true,
			'popup' => view('lms.lessons.popup')->with(['video' => $video, 'lesson' => $lesson])->render()
		]);
	}

	public function testPopup(Lesson $lesson)
	{
		if(!empty($lesson->q_answered))
		{
			return response()->json([
				'status' => true,
				'popup' => view('lms.lessons.popup')->with(['video' => $lesson->q_answered])->render()
			]);
		}

		return response()->json([
			'status' => true
		]);
	}

	public function assessmentCheck(Lesson $lesson)
	{
		$user_id = request()->get('user_id');
		$test_id = request()->get('test_id');
		$taken = request()->get('taken');
		$response = ['first_time' => false];

		$row = DB::table('class_marker_results')->where('user_id', $user_id)->where('assessment_id', $test_id)->first();
        $response['status'] = !empty($row);
        $response['score'] = !empty($row) ? $row->score : 0;
		if(!empty($row))
		{
            if(!empty($taken) && $row->created_at == $taken)
            {
                $response['status'] = false;
            }

            if($row->created_at == $row->passed_at)
            {
                $response['first_time'] = true;
                DB::table('class_marker_results')->where('user_id', $user_id)->where('assessment_id', $test_id)->update(['passed_at' => Carbon::now()]);
            }

			return response()->json($response);
		}

		return response()->json($response);
	}

	public function viewResultsVideoPopup($lessonQuestion)
	{
		$lessonQuestion = LessonQuestion::findOrFail($lessonQuestion);

		return view('lms.lessons.resultVideoPopup')->with('video', $lessonQuestion);
	}

    public function classMarkerResults(Request $request)
    {
		http_response_code(200);

		$result = $request->input('result');
		$test = $request->input('test');

		$user_id = (int) $result['cm_user_id'];
		$test_id = (int) $test['test_id'];
		$score = (int) $result['percentage'];
		$cert_url = !empty($result['certificate_url']) ? $result['certificate_url'] : '';
		
		$passed = $result['passed'];
        $user = \App\Models\User::find($user_id);
        $contact_id = $user->contact_id;

		$row = DB::table('class_marker_results')->where('user_id', $user_id)->where('assessment_id', $test_id)->first();

		$q = LessonQuestion::where('assessment_id', $test_id)->first();
		if($q)
		{
			if($passed && !empty($q->assessment_pass_tags))
			{
				InfusionsoftFlow::addTag($contact_id, explode(',', $q->assessment_pass_tags));
			}

			if(!$passed && !empty($q->assessment_fail_tags))
			{
				InfusionsoftFlow::addTag($contact_id, explode(',', $q->assessment_fail_tags));
			}
		}

        $now = Carbon::now();

		if(!$row)
		{
			DB::table('class_marker_results')->insert([
				'user_id' => $user_id,
				'course_id' => $q->lesson->course->id,
				'assessment_id' => $test_id,
				'score' => $score,
				'passed' => $passed,
                'cert_url' => $cert_url,
                'created_at' => $now,
                'passed_at' => $passed ? $now : null
			]);

            $log = new \App\Models\Log;
            $log->user_id = $user_id;
            if ($passed) {
                $log->action_id = 9;
            } else {
                $log->action_id = 11;
            }
            $log->activity_id = 6;
            $log->save();
            $q->lesson->course->logs()->save($log);

		}else{
		    $update = [
                'score' => $score,
                'passed' => $passed
            ];

		    if($passed && empty($row->passed_at))
            {
                $update['passed_at'] = $now;
                $update['created_at'] = $now;
            }

		    if(!empty($cert_url))
            {
                $update['cert_url'] = $cert_url;
            }

            $log = new \App\Models\Log;
            $log->user_id = $user_id;
            if ($passed) {
                $log->action_id = 10;
            } else {
                $log->action_id = 12;
            }
            $log->activity_id = 6;
            $log->save();
            $q->lesson->course->logs()->save($log);

			DB::table('class_marker_results')->where('user_id', $user_id)->where('assessment_id', $test_id)->update($update);
		}
    }
}
