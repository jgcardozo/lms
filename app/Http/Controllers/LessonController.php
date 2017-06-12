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
        //
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
}
