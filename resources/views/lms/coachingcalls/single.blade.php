@extends('layouts.app')

@section('title', $course->title)

@section('scripts_before')
    <script src="//fast.wistia.com/assets/external/E-v1.js" async></script>
@endsection

@section('content')
    <main>
        <div class="grid grid--full course-single" @if($course->featured_image) style="background-image: url({{ $course->getFeaturedImageUrlAttribute() }});" @endif>
            <div class="course-single__overlay"></div>

            <div class="grid grid--w950 course-single__content">
                <div class="course-single__content-wrap grid--flex flex--space-between">
                    <div class="single-header-block">
                        <div class="single-header-block__step-back">
                            <a href="{{ route('single.course', $course->slug) }}">
                                Back to <strong>{!! $course->title !!}</strong>
                            </a>
                        </div>

                        <h2 class="single-header-block__title">Your Q&A Coaching Calls</h2>
                        <div class="single-header-block__content single-header-block__content--small">
                            <p>As a special BONUS, we’ve got LIVE Q&A Calls planned for you throughout the Masterclass…</p>

                            <p>This is your chance to ASK me ANYTHING when it comes to applying the ASK Method to YOUR business.</p>
                        </div>
                    </div>

                    <div class="single-header-video">
                         {{--<div class="wistia_responsive_padding">
                             <div class="wistia_responsive_wrapper">
                                 <div class="wistia_embed wistia_async_{{ $course->video_url }}"></div>
                             </div>
                         </div>--}}
                        <img style="display: block; max-width: 100%;" src="{{ asset('images/thumb-coaching.png') }}" />
                    </div>
                </div>
            </div>
        </div>
        
        <div class="grid grid--w950">
            <div class="lesson-sessions">
                <h2 class="lesson-sessions__title">Coaching Calls</h2>

                <div class="lesson-sessions__list">
                    @foreach($coaching_calls as $key => $coaching_call)
                        <div id="session-{{ $coaching_call->id }}" class="lesson-sessions__item grid--flex flex--space-between">
                            @if($coaching_call->is_date_locked)
                                <div class="lesson-sessions__item--locked-overlay"></div>
                            @endif
                            <div class="lesson-sessions__video grid--flex">
                                @if($coaching_call->is_date_locked)
                                    <div class="course-progress grid--flex flex--align-center flex--just-center">
                                        <span class="course-progress__bar course-progress__bar--locked"></span>
                                    </div>
                                @else
                                    <a href="#" data-href="{{ route('coachingcall.show', [$course->slug, $coaching_call->id]) }}" class="block__link js-open-session"></a>
                                @endif
                            </div>

                            <div class="lesson-sessions__content grid--flex flex--space-between flex--align-center">
                                <div class="lesson-sessions__content--left">
                                    <h2 class="lesson-sessions__item--title">{{ $coaching_call->title }}</h2>

                                    <p>{{ truncate_string($coaching_call->description) }}</p>
                                </div>

                                <div class="lesson-sessions__content--right">
                                    @if($coaching_call->is_completed)
                                        <div class="course-progress course-progress--completed">Completed <span class="course-progress__bar course-progress__bar--completed"></span></div>                                    
                                    @elseif($coaching_call->is_date_locked)
                                        <div class="course-progress" data-date=" until {{ date('d-m-Y', strtotime($coaching_call->lock_date)) }}">
                                            Unlocks {{ date('d-m-Y', strtotime($coaching_call->lock_date)) }} 
                                        </div>
                                    @else
                                        <div style="{{ $coaching_call->video_progress >= 80 ? '' : 'display: none;' }}" class="course-progress" data-complete="{{ route('coachingcall.completed', [$course->slug, $coaching_call->id]) }}">Mark as completed <span class="course-progress__bar"></span></div>
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

            <div class="session-single__content-ajax">

            </div>

        </div>
    </div>
@endsection
