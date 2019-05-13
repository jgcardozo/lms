<?php

namespace App\Http\Controllers;

use App\Models\Cohort;
use App\Models\Course;
use Illuminate\Http\Request;

class EasterLinksController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $courses = Course::all();
        $course = $courses->find(3);
        $cohorts = Cohort::with(['fbLinksLesson','fbLinksCourse'])->get();
        return view('lms.admin.easter_egg_link',compact('course','cohorts','courses'));
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
        $cohort = Cohort::find($request->get('cohort'));
        $lessons = $request->get('lessons');
        $courses = $request->get('courses');
        $filteredLessons = [];
        $filteredCourses = [];

        foreach (array_filter($lessons) as $id => $link) {
            $filteredLessons[$id] = ['fb_link' => $link];
        }

        foreach (array_filter($courses) as $id => $link) {
            $filteredCourses[$id] = ['fb_link' => $link];
        }

        $cohort->fbLinksLesson()->sync($filteredLessons);
        $cohort->fbLinksCourse()->sync($filteredCourses);

        return redirect()->back()->withInput();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $cohort = Cohort::with(['fbLinksLesson','fbLinksCourse'])->find($id);
        $fbLinkLessons = $cohort->fbLinksLesson->pluck('pivot');
        $fbLinkCourses = $cohort->fbLinksCourse->pluck('pivot');

        return response()->json([
            "fbLinkLessons" => $fbLinkLessons,
            "fbLinkCourses" => $fbLinkCourses
        ],200);
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
