@extends('lms.master')

@section('title', $lesson->title)

@section('content')
    <div class="single-header-block">
        <div class="single-header-block__step-back">
            <a href="{{ route('single.module', $lesson->module->slug) }}">
                Back to {{ $lesson->module->title }}
            </a>
        </div>

        <h2 class="single-header-block__title">{{ $lesson->title }}</h2>
        <div class="single-header-block__content">
            {!! $lesson->description !!}
        </div>
    </div>

    <div class="lesson-sessions">
        <h2 class="lesson-sessions__title">Sessions</h2>

        <div class="lesson-sessions__list">
            @foreach($lesson->sessions as $key => $session)
                {!! $session->featured_image_url !!}
                <div id="session-{{ $session->id }}" class="block">
                    <h2 class="block__title">{{ $session->title }}</h2>

                    {!! $session->description !!}

                    @if($session->is_completed)
                        <span class="completed">Completed</span>
                    @endif

                    @if($session->is_date_locked)
                        <div class="locked" data-date=" until {{ date('d-m-Y', strtotime($session->lock_date)) }}"></div>
                    @endif

                    <a href="{{ route('session.completed', $session->slug) }}" class="block__link">Watch</a>
                </div>
            @endforeach
        </div>
    </div>
@endsection