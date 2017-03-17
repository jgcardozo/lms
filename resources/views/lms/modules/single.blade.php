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
                                Back to <strong>{{ $module->course->title }}</strong>
                            </a>
                        </div>

                        <h2 class="single-header-block__title">{{ $module->title }}</h2>
                        <div class="single-header-block__content single-header-block__content--small">
                             {!! $module->description !!}
                        </div>
                    </div>

                    <div class="single-header-video">
                         <script src="//fast.wistia.com/assets/external/E-v1.js" async></script>
                         <div class="wistia_responsive_padding" style="padding:56.67% 0 0 0;position:relative;">
                             <div class="wistia_responsive_wrapper" style="height:100%;left:0;position:absolute;top:0;width:100%;">
                                 <div class="wistia_embed wistia_async_gpc49zomb2" style="width:100%;height:100%;"></div>
                             </div>
                         </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="grid grid--w950">
            <div class="lessons-list">
                <h2 class="module-lessons__title">Lessons</h2>

                <div class="module-lessons__list">
                    @foreach($module->lessons as $key => $lesson)
                        <div id="lesson-{{ $lesson->id }}" class="module-lessons__item grid--flex flex--space-between lesson {{ ($key % 3) == 0 ? 'lesson--first' : '' }}">
                            <div class="lessons-list__content grid--flex flex--space-between flex--align-center">
                                <div class="lessons-list__content--left">
                                    <h2 class="lessons-list__item--title"><a href="{{ route('single.lesson', $lesson->slug) }}">{{ $lesson->title }}</a></h2>

                                    <h5>Progress {{ $lesson->getProgressPercentage() }}%</h5>

                                    <p>{{ truncate_string($lesson->description) }}</p>
                                </div>

                                <div class="lessons-list__content--center grid--flex flex--space-between">
                                    <div class="lessons-list__lesson-info">
                                        <p>Sessions</p>
                                        <h4>12</h4>
                                    </div>

                                    <div class="lessons-list__lesson-info">
                                        <p>Avg. Time</p>
                                        <h4>1h</h4>
                                    </div>                                     
                                </div>
                                
                                <div class="lessons-list__content--right">
                                    @if($lesson->is_completed)
                                        <div class="course-progress course-progress--completed">Completed <span class="course-progress__bar course-progress__bar--completed"></span></div>
                                    @elseif($lesson->is_date_locked)
                                        <div class="course-progress" data-date=" until {{ date('d-m-Y', strtotime($lesson->lock_date)) }}">
                                            <p>Unlocks {{ date('d-m-Y', strtotime($lesson->lock_date)) }}</p>
                                        </div>
                                    @else
                                        <div class="course-progress"><span class="course-progress__bar"></span></div>
                                    @endif
                                </div>                                
                            </div>                            
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </main>
@endsection