<?php

namespace App\Http\Controllers;

use DB;
use Auth;
use App\Models\Course;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($slug)
    {
        $course = Course::with('modules')->whereSlug($slug)->first();

        if(!$course) {
            abort(404);
        }

        $viewArgs = [
			'course' => $course,
			'nextSession' => $course->getNextSession(),
			'starterSeen' => $course->areAllStarterSeen()
		];
dd($viewArgs);
        if(count($course->modules) == 1) {
            return redirect()->route('single.module', $course->modules[0]->slug);
        }

		// Popup before course start
        if(survey_check($course)) {
            $viewArgs['popupBefore'] = 'lms.survey';
        }

        return view('lms.courses.single')->with($viewArgs);
    }

    public function starter_videos($slug)
    {
        $course = Course::findBySlugOrFail($slug);

        if(!$course) {
            abort(404);
        }

        $videos = $course->starter_videos;

        return view('lms.courses.starter')->with(['course' => $course, 'videos' => $videos]);
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
}
