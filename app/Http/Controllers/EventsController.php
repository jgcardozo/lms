<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
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
        $events = Event::orderBy('start_date','asc')->get();
        $courses = Course::get();

		return view('lms.calendar.index')
				->with('allEvents', $events)
				->with('futureEvents', $events->where('start_date', '>', Carbon::now()->startOfDay()))
				->with('courses', $courses);
    }

    public function filterCourse(Request $request)
    {
        if($request->has('course'))
        {
            $course_id = $request->get('course');
            $course = Course::find($course_id);

			if(is_null($course))
			{
				$allEvents = Event::get();
			}else{
				$allEvents = $course->events;
			}

			$futureEvents = $allEvents->where('start_date', '>', Carbon::now()->startOfDay());

            return [
				'view' => (string) view('lms.calendar.inc.index')->with('futureEvents', $futureEvents),
				'events' => $allEvents->pluck('start_date')->map(function($item, $key) { return date('Y-m-d', strtotime($item)); })->toJson()
			];
        }

        return false;
    }

	public function filterDate(Request $request)
	{
		if($request->has('date'))
		{
			$date = $request->get('date');

			$events = Event::whereBetween(
								'start_date',
								[
									Carbon::parse($date . ' 00:00:00')->toDateTimeString(),
									Carbon::parse($date . ' 23:59:59')->toDateTimeString()
								])->get();

			return view('lms.calendar.inc.index')->with('futureEvents', $events);
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
