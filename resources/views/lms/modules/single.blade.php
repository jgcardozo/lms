@extends('layouts.app')

@section('title', $module->title)

@section('content')
    <main>
        <div class="grid grid--full course-single">
            <div class="course-single__overlay"></div>

            <div class="grid grid--w950 course-single__content">
                <div class="grid--flex flex--space-between">
                    <div class="single-header-block">
                        <div class="single-header-block__step-back">
                            <a href="{{ route('single.course', $module->course->slug) }}">
                                Back to {{ $module->course->title }}
                            </a>
                        </div>

                        <h2 class="single-header-block__title">{{ $module->title }}</h2>
                        <div class="single-header-block__content">
                            {!! $module->description !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="grid grid--w950">
            <div class="module-lessons">
                <h2 class="module-lessons__title">Lessons</h2>

                <div class="module-lessons__list">
                    @foreach($module->lessons as $key => $lesson)
                        <div id="lesson-{{ $lesson->id }}" class="block lesson {{ ($key % 3) == 0 ? 'lesson--first' : '' }}">
                            @if($lesson->is_locked)
                                <div class="locked"
                                    @if($lesson->is_date_locked)
                                        data-date=" until {{ date('d-m-Y', strtotime($lesson->lock_date)) }}"
                                    @endif
                                ></div>
                            @endif
                            <h2 class="block__title">{{ $lesson->title }}</h2>

                            @if($lesson->is_completed)
                                <span class="completed">Completed</span>
                            @endif

                            {!! $lesson->description !!}

                            <a href="{{ route('single.lesson', $lesson->slug) }}" class="block__link">View lesson</a>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </main>
@endsection