<?php

namespace App\Http\Controllers;

use DB;
use Auth;
use App\Models\User;
use InfusionsoftFlow;
use App\Models\Lesson;
use Illuminate\Http\Request;
use App\Models\LessonQuestion;

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
		$lesson->usersPosted()->attach(Auth::user()->id);

        mixPanel()->track('Posted to Facebook', [
            'lesson_id' => $lesson->id,
            'lesson' => $lesson->title,
            'course' => $lesson->course ? $lesson->course->title : '',
            'module' => $lesson->module ? $lesson->module->title : ''
        ]);

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

		if($lesson->q_answered)
		{
			$row = DB::table('class_marker_results')->where('user_id', $user)->where('assessment_id', $lesson->q_answered->assessment_id)->first();
			$score = $row && $row->passed ? $row->score : null;
		}

		return view('lms.lessons.result')->with(['lesson' => $lesson, 'score' => $score]);
	}
    
    public function answerQuestion(Lesson $lesson)
	{
		$qID = request()->get('question');
		$user = Auth::user();

		$lesson->userAnswered()->attach([$user->id => ['question_id' => $qID]]);
		$video = LessonQuestion::find($qID);

		InfusionsoftFlow::addTag($user->contact_id, explode(',', $video->is_tags));

		return response()->json([
			'status' => true,
			'popup' => view('lms.lessons.popup')->with(['video' => $video])->render()
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

		$row = DB::table('class_marker_results')->where('user_id', $user_id)->where('assessment_id', $test_id)->first();

		return response()->json([
			'status' => !empty($row)
		]);
	}

    public function classMarkerResults(Request $request)
    {
		http_response_code(200);

		$result = request()->get('result');
		$test = request()->get('test');

		$user_id = (int) $result['cm_user_id'];
		$test_id = (int) $test['test_id'];
		$score = (int) $result['percentage'];
		$passed = $result['passed'];
		$user = User::find($user_id);

		$row = DB::table('class_marker_results')->where('user_id', $user_id)->where('assessment_id', $test_id)->first();

		$q = LessonQuestion::where('assessment_id', $test_id)->take(1)->get();
		if($q)
		{
			if($passed)
			{
				// InfusionsoftFlow::addTag($user->contact_id, explode(',', $q->assessment_pass_tags));
			}else{
				// InfusionsoftFlow::addTag($user->contact_id, explode(',', $q->assessment_fail_tags));
			}
		}

		if(!$row)
		{
			DB::table('class_marker_results')->insert([
				'user_id' => $user_id,
				'assessment_id' => $test_id,
				'score' => $score,
				'passed' => $passed
			]);
		}else{
			DB::table('class_marker_results')->where('user_id', $user_id)->where('assessment_id', $test_id)->update([
				'score' => $score,
				'passed' => $passed
			]);
		}
    }
}
