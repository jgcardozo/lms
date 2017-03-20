@extends('layouts.app')

@section('title', 'Calendar')

@section('content')
    @if(! empty($events))
        <main class="grid grid--w950 spacer__top--big">
            <h1 class="page--title">Events</h1>

            <section class="grid--flex flex--column">
                @foreach($events as $event)
                    <div class="event {{ $event->course->is_locked ? 'locked' : '' }}">
                        <h3>{{ \Carbon\Carbon::parse($event->start_date)->format('F m') }}</h3>
                        <h5>{{ $event->course->title }}</h5>
                        <h4>{{ $event->title }}</h4>
                        <p>{!! $event->description !!}</p>
                    </div>
                @endforeach
            </section>
        </main>
    @endif
@endsection