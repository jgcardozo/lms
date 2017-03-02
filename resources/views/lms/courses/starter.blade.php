@extends('lms.master')

@section('title', $course->title)

@section('content')
    <div class="single-header-block">
        <h2 class="single-header-block__title">{{ $course->title }}</h2>
        <p class="single-header-block__content">{{ $course->short_description }}</p>
        <div class="single-header-block__separator"></div>
        <div class="single-header-block__content single-header-block__content--small">
            {!! $course->description !!}
        </div>
    </div>

    <div class="lesson-sessions">
        <h2 class="lesson-sessions__title">Getting started</h2>

        <div class="lesson-sessions__list">
            @foreach($videos as $video)
                <div id="session-{{ $video->id }}" class="block">
                    <h2 class="block__title">{{ $video->title }}</h2>

                    {!! $video->description !!}

                    @if($video->is_completed)
                        <span class="completed">Completed</span>
                    @endif

                    <a href="{{ route('session.completed', $video->slug) }}" class="block__link">Watch</a>
                </div>
            @endforeach
        </div>
    </div>
@endsection