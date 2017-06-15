<?php

namespace App\Http\Controllers;

use Auth;
use App\Models\Lesson;
use App\Models\LessonQuestion;
use Illuminate\Http\Request;

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

		if(!$lesson) {
			abort(404);
		}

		return view('lms.lessons.result')->with(['lesson' => $lesson]);
	}
    
    public function answerQuestion(Lesson $lesson)
	{
		$qID = request()->get('question');

		//$lesson->userAnswered()->attach([Auth::user()->id => ['question_id' => $qID]]);
		$video = LessonQuestion::find($qID);

		return response()->json([
			'status' => true,
			'popup' => view('lms.lessons.popup')->with(['video' => $video])->render()
		]);
	}

    public function classMarkerResults(Request $request)
    {
        http_response_code(200);

        \Storage::disk('local')->put('classMarkerResults.txt', print_r($request->all(), true));
    }
}
