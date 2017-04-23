<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CoachingCall;
use Illuminate\Http\Request;

class CoachingCallController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($slug)
    {
        $course = Course::findBySlugOrFail($slug);
        $coaching_calls = $course->coachingcall;

        return view('lms.coachingcalls.single')->with(['coaching_calls' => $coaching_calls])->with(['course' => $course]);
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
     * @param  \App\Models\CoachingCall  $coachingCall
     * @return \Illuminate\Http\Response
     */
    public function show($course, $id)
    {
        $coaching_call = CoachingCall::findOrFail($id);

        return view('lms.coachingcalls.session-popup')->with('coaching_call', $coaching_call);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\CoachingCall  $coachingCall
     * @return \Illuminate\Http\Response
     */
    public function edit(CoachingCall $coachingCall)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\CoachingCall  $coachingCall
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CoachingCall $coachingCall)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\CoachingCall  $coachingCall
     * @return \Illuminate\Http\Response
     */
    public function destroy(CoachingCall $coachingCall)
    {
        //
    }
}
