@extends('layouts.app')

@section('title', $course->title)

@section('content')
    <div class="single-header-block">
        <h2 class="single-header-block__title">{{ $course->title }}</h2>
        <p class="single-header-block__content">{{ $course->short_description }}</p>
        <div class="single-header-block__separator"></div>
        <div class="single-header-block__content single-header-block__content--small">
            {!! $course->description !!}
        </div>

        <div class="block block--course-reminder">
            @if(!$starterSeen)
                <span><em>Hi there</em></span>
                <p>Welcome to {{ $course->title }}</p>
                <span>{{ $course->short_description }}</span>
                <a href="{{ route('single.course.starter', $course->slug) }}" class="watch-them">Watch videos</a>
            @elseif(!empty($nextSession))
                <span><em>Last Session</em></span>
                <p>Welcome to {{ $nextSession->title }}</p>
                <span>{{ strip_tags($nextSession->description) }}</span>
                <a href="{{ route('single.lesson', $nextSession->lesson->slug) }}" class="watch-them">Resume</a>
            @else
                <span><em>Congrats</em></span>
                <p>You watch them all, go to hell</p>
                <span></span>
            @endif
        </div>
    </div>

    <div class="course-modules">
        <h2 class="course-modules__title">{{ $course->module_group_title }}</h2>

        <div class="course-modules__list">
            @foreach($course->modules as $key => $module)
                <div id="module-{{ $module->id }}" class="block module {{ ($key % 3) == 0 ? 'module--first' : '' }}">
                    @if($module->is_locked)
                        <div class="locked"
                            @if($module->is_date_locked)
                                data-date=" until {{ date('d-m-Y', strtotime($module->lock_date)) }}"
                            @endif
                        ></div>
                    @endif
                    <h2 class="block__title">{{ $module->title }}</h2>

                    @if($module->is_completed)
                        <span class="completed">Completed</span>
                    @endif

                    {!! $module->description !!}

                    <a href="{{ route('single.module', $module->slug) }}" class="block__link">View module</a>
                </div>
            @endforeach
        </div>
    </div>
@endsection