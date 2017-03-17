@extends('layouts.app')

@section('title', $lesson->title)

@section('content')
    <main>
        <div class="grid grid--full course-single">
            <div class="course-single__overlay"></div>

            <div class="grid grid--w950 course-single__content">
                <div class="grid--flex flex--space-between">
                    <div class="single-header-block">
                        <div class="single-header-block__step-back">
                            <a href="{{ route('single.module', $lesson->module->slug) }}">
                                Back to <strong>{{ $lesson->module->title }}</strong>
                            </a>
                        </div>

                        <h2 class="single-header-block__title">{{ $lesson->title }}</h2>
                        <div class="single-header-block__content single-header-block__content--small">
                            {!! $lesson->description !!}
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
            <div class="lesson-sessions">
                <h2 class="lesson-sessions__title">Sessions</h2>

                <div class="lesson-sessions__list">
                    @foreach($lesson->sessions as $key => $session)
                        <div id="session-{{ $session->id }}" class="lesson-sessions__item grid--flex flex--space-between">
                            <div class="lesson-sessions__video grid--flex">
                                <a href="{{ route('session.completed', $session->slug) }}" class="block__link js-open-session"></a>
                            </div>

                            <div class="lesson-sessions__content grid--flex flex--space-between flex--align-center">
                                <div class="lesson-sessions__content--left">
                                    <h2 class="lesson-sessions__item--title">{{ $session->title }}</h2>

                                    <p>{{ truncate_string($session->description) }}</p>
                                </div>

                                <div class="lesson-sessions__content--right">
                                    @if($session->is_completed)
                                        <div class="course-progress course-progress--completed">Completed <span class="course-progress__bar course-progress__bar--completed"></span></div>
                                    @elseif($session->is_date_locked)
                                        <div class="course-progress course-progress--locked" data-date=" until {{ date('d-m-Y', strtotime($session->lock_date)) }}">
                                            <p>Unlocks {{ date('d-m-Y', strtotime($session->lock_date)) }}</p>
                                        </div>
                                    @else
                                        <div class="course-progress">Mark as completed <span class="course-progress__bar"></span></div>
                                    @endif
                                </div>                                
                            </div>                            
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </main>

    <div class="session-single">
        <div class="session-single__content">
            <div class="session-single__close"></div>

            @include('lms.courses.session-popup')

        </div>
    </div>
@endsection
