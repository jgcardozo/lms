<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Course;
use Illuminate\Http\Request;

class EventsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $events = Event::get();
        $courses = Course::get();

		return view('lms.calendar.index')->with('events', $events)->with('courses', $courses);
    }

    public function filterCourse(Request $request)
    {
        if($request->has('course'))
        {
            $course_id = $request->get('course');
            $course = Course::find($course_id);

			if(is_null($course))
			{
				$events = Event::get();
			}else{
				$events = $course->events;
			}

            return [
				'view' => (string) view('lms.calendar.inc.index')->with('events', $events),
				'events' => $events->pluck('start_date')->map(function($item, $key) { return date('Y-m-d', strtotime($item)); })->toJson()
			];
        }

        return false;
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
     * @param  \App\Models\Event  $events
     * @return \Illuminate\Http\Response
     */
    public function show(Event $event)
    {
		return view('lms.calendar.popup')->with('event', $event);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Event  $events
     * @return \Illuminate\Http\Response
     */
    public function edit(Event $event)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Event  $events
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Event $event)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Event  $events
     * @return \Illuminate\Http\Response
     */
    public function destroy(Event $event)
    {
        //
    }
}
