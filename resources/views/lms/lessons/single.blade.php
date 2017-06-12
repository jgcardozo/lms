@extends('layouts.app')

@section('title', $lesson->title)

@section('scripts_before')
    <script src="//fast.wistia.com/assets/external/E-v1.js" async></script>
@endsection

@section('content')
    <main>
        <div class="grid grid--full course-single" @if($lesson->featured_image) style="background-image: url({{ $lesson->featured_image_url }});" @endif>
            <div class="course-single__overlay"></div>

            <div class="grid grid--w950 course-single__content">
                <div class="course-single__content-wrap grid--flex flex--space-between">
                    <div class="single-header-block">
                        <div class="single-header-block__step-back">
                            @if(count($lesson->module->lessons) > 1)
                                <a href="{{ route('single.module', $lesson->module->slug) }}">
                                    Back to <strong>{{ $lesson->module->title }}</strong>
                                </a>
                            @endif
                        </div>

                        <h2 class="single-header-block__title">{{ $lesson->title }}</h2>
                        <div class="single-header-block__content single-header-block__content--small">
                            {!! $lesson->description !!}
                        </div>
                    </div>

                    <div class="single-header-video">
                         <div class="wistia_responsive_padding">
                             <div class="wistia_responsive_wrapper">
                                 <div class="wistia_embed wistia_async_{{ $lesson->video_url }}"></div>
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
                            @if($session->is_date_locked)
                                <div class="lesson-sessions__item--locked-overlay"></div>
                            @endif
                            <div class="lesson-sessions__video grid--flex" @if($session->featured_image) style="background-image: url({{ $session->featured_image_url }});" @endif>
                                @if($session->is_date_locked)
                                    <div class="course-progress grid--flex flex--align-center flex--just-center">
                                        <span class="course-progress__bar course-progress__bar--locked"></span>
                                    </div>
                                @else
                                    <a href="#" data-href="{{ route('session.show', $session->id) }}" class="block__link js-open-session"></a>
                                @endif
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
                                        <div class="course-progress" data-date=" until {{ date('d-m-Y', strtotime($lesson->getDate('lock_date'))) }}">
                                            Unlocks {{ date('d-m-Y', strtotime($lesson->getDate('lock_date'))) }}
                                        </div>
                                    @else
                                        <div style="{{ $session->video_progress || is_role_admin() >= 80 ? '' : 'display: none;' }}" class="course-progress" data-complete="{{ route('session.completed', $session->id) }}">Mark as completed <span class="course-progress__bar"></span></div>
                                    @endif
                                </div>                                
                            </div>                            
                        </div>
                    @endforeach
                </div>

                @if($lesson->has_bonus && $lesson->questions->isEmpty())
                    @if(!$lesson->is_fb_posted)
                        <div class="lesson-sessions__item lesson-sessions__item--bonus js-bonus" style="@if(!$lesson->is_completed) display: none @endif">
                            <p>Awesome! You have finished this Lesson. Time to unlock a hidden bonus by answering a simple question: <strong>What was your biggest takeaway from this module?</strong></p>

                            <form method="post" class="js-lesson-post-to-facebook" data-fburl="{{ $lesson->fb_link }}" action="{{ route('lesson.postToFacebook', $lesson->id) }}">
                                {{ csrf_field() }}
                                <input type="submit" value="Post to Facebook">
                            </form>
                        </div>
                    @else
                        <div class="lesson-sessions__item lesson-sessions__item--bonus">
                            <p>Thank you! Special Bonus Content unlocked!</p>

                            <div class="grid--flex flex--space-between">
                                <div class="lesson-sessions__item--bonus-video">
                                    <div class="wistia_responsive_padding">
                                        <div class="wistia_responsive_wrapper">
                                            <div class="wistia_embed wistia_async_{{ $lesson->bonus_video_url }}"></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="lesson-sessions__item--bonus-content">
                                    {!! $lesson->bonus_video_text !!}
                                </div>
                            </div>
                        </div>
                    @endif
                @elseif(!$lesson->questions->isEmpty())
                    <div class="lesson-sessions__item lesson-sessions__item--bonus js-bonus">
                        <p><strong>Awesome! You have finished this Lesson. Time to unlock a hidden bonus by answering a simple question: What was your biggest takeaway from this module?</strong></p>

                        <form method="post" class="js-lesson-answer-question" action="{{ route('lesson.answerQuestion', $lesson->id) }}">
                            {{ csrf_field() }}
                            @foreach($lesson->questions as $question)
                                <div class="lesson__question-radio">
                                    <input type="radio" id="question-{{ $question->id }}" name="question" value="{{ $question->id }}" />
                                    <label for="question-{{ $question->id }}">{{ $question->question }}</label>
                                </div>
                            @endforeach
                            <input type="submit" value="Next">
                        </form>
                    </div>
                @endif
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
