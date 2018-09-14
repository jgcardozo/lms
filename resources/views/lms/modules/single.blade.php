@extends('layouts.app')

@section('title', $module->title)

@section('scripts_before')
    <script src="//fast.wistia.com/assets/external/E-v1.js" async></script>
@endsection

@section('content')
    <main>
        <div class="grid grid--full course-single" @if($module->featured_image) style="background-image: url({{ $module->featured_image_url }});" @endif>
            <div class="course-single__overlay"></div>

            <div class="grid grid--w950 course-single__content">
                <div class="course-single__content-wrap grid--flex flex--space-between">
                    <div class="single-header-block">
                        <div class="single-header-block__step-back">
                            @if(count($module->course->modules) > 1)
                                <a href="{{ route('single.course', $module->course->slug) }}">
                                    Back to <strong>{!! $module->course->title !!}</strong>
                                </a>
                            @endif
                        </div>

                        <h2 class="single-header-block__title">{{ $module->title }}</h2>
                        <div class="single-header-block__content single-header-block__content--small">
                             {!! $module->description !!}
                        </div>
                    </div>

                    <div class="single-header-video">
                         <div class="wistia_responsive_padding">
                             <div class="wistia_responsive_wrapper">
                                 @include('lms.components.video', ['model' => $module])
                             </div>
                         </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="grid grid--w950">
            <div class="lessons-list">
                <h2 class="module-lessons__title">{{ $module->lesson_group_title }}</h2>

                <div class="module-lessons__list">
                    @foreach($module->lessons as $key => $lesson)
                        <div id="lesson-{{ $lesson->id }}" class="module-lessons__item grid--flex flex--space-between lesson {{ ($key % 3) == 0 ? 'lesson--first' : '' }}">
                            <div class="lessons-list__content grid--flex flex--space-between flex--align-center">
                                @if($lesson->is_locked)
                                    <div class="lessons-list__content--locked-overlay"></div>
                                @endif

                                <div class="lessons-list__content--left">
                                    @if($lesson->is_locked)
                                        <h2 class="lessons-list__item--title">{{ $lesson->title }}</h2>
                                    @else
                                        <h2 class="lessons-list__item--title"><a href="{{ route('single.lesson', $lesson->slug) }}">{{ $lesson->title }}</a></h2>
                                    @endif
                                    <p>{!!  truncate_string($lesson->description) !!}</p>
                                </div>

                                <div class="lessons-list__content--center grid--flex flex--space-between">
                                    <div class="lessons-list__lesson-info">
                                        <p>Sessions</p>
                                        <h4>{{ count($lesson->sessions) }}</h4>
                                    </div>

                                    <div class="lessons-list__lesson-info">
                                        <p>Avg. Time</p>
                                        <h4>{{ $lesson->duration }}m</h4>
                                    </div>                                     
                                </div>

                                <div class="lessons-list__content--right">
                                    @if($lesson->is_completed && $lesson->isCompleteVideoFeatureOn())
                                        <div class="course-progress course-progress--completed course-progress__lesson">Completed <span class="course-progress__bar course-progress__bar--completed"></span></div>
                                    @elseif($lesson->is_locked)
                                        @if($lesson->is_date_locked)
                                            <div class="course-progress course-progress__lesson" data-date=" until {{ date('d-m-Y', strtotime($lesson->lock_date)) }}">
                                               <span> Unlocks {{ Auth::user()->UnlockDate($lesson) }}</span> <span class="course-progress__bar course-progress__bar--locked"></span>
                                            </div>
                                        @else
                                            <div class="course-progress course-progress__lesson">
                                               <span class="course-progress__bar course-progress__bar--locked"></span>
                                            </div>
                                        @endif
                                    @else
                                        @if($lesson->isCompleteVideoFeatureOn())
                                            <div class="course-progress course-progress__lesson"><span class="course-progress__bar course-progress__bar--active" data-percentage="{!! $lesson->getProgressPercentage() / 100 !!}"></span></div>
                                        @endif
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