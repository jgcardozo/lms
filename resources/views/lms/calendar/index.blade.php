@extends('layouts.app')

@section('title', 'Calendar')

@section('content')
	<main class="grid grid--w950 spacer__top--big">
		<h1 class="page--title">Calendar</h1>

		<section class="grid--flex flex--column">
			<div class="calendar">
                <div class="calendar__filter">
                    <p>View</p>
                    <select class="js-calendar-filter" data-route="{{ route('calendar.filter.course') }}">
                        <option value="0">All Courses</option>
                        @foreach($courses as $course)
                            <option value="{{ $course->id }}">{!! $course->title !!}</option>
                        @endforeach
                    </select>
                </div>
				<div id="datepicker"></div>
			</div>

			@include('lms.calendar.inc.index')
		</section>
	</main>

    <div class="event-single">
        <div class="event-single__content">
            <div class="event-single__close"></div>

            <div class="event-single__content-ajax">

            </div>

        </div>
    </div>
@endsection

@section('scripts_before')
    <script>
        window.calendar_events = '{!! $allEvents->pluck('start_date')->map(function($item, $key) { return date('Y-m-d', strtotime($item)); })->toJson() !!}';
    </script>
@endsection