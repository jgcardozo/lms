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
                    @if(!empty($lesson->q_answered))
                        @if(!$lesson->test_finished)
                            <div class="lesson-sessions__item lesson-sessions__item--bonus grid--flex flex--align-center">
                                <div class="lesson-sessions__item__student-image">
                                    <img src="{{ URL::to('/') }}/images/icons/student_happy.svg" />
                                </div>

                                <div class="lesson-sessions__item__content">
                                    <p><strong>Congratulations!</strong> You’ve made it to the end of the Masterclass!</p>
                                    <p>Just like any great course, you have your final exam (it’s not hard - it’s actually pretty fun!).</p>
                                    <p class="margin">Take the course assessment now to see how much you’ve learned and unlock even more surprises!</p>
                                    <a target="_blank" href="{{ route('single.lesson.assessment.results', $lesson->slug) }}" data-user="{{ \Auth::user()->id }}" data-url="{{ route('single.lesson.assessment.check') }}" data-test="{{ $lesson->q_answered->assessment_id }}" data-popup="{{ route('lesson.testPopup', $lesson->id) }}" class="session-single__content-learn__default-btn-link js-retake-assessment">Take the assessment to see how much you've learned</a>
                                </div>
                            </div>
                        @else
                            <div class="lesson-sessions__item lesson-sessions__item--bonus grid--flex flex--align-center">
                                <div class="lesson-sessions__item__student-image">
                                    @if($lesson->test_finished->passed)
                                        <img src="{{ URL::to('/') }}/images/icons/student_happy.svg" />
                                        <h3 style="text-align: center; margin: 0; font-size: 4rem; margin-top: -40px; text-shadow: 0 0 10px #b1b1b1;">{{ number_format(($lesson->test_finished->score / 10), 1, '.', ',') }}</h3>
                                    @else
                                        <img src="{{ URL::to('/') }}/images/icons/student_sad.svg" />
                                    @endif
                                </div>

                                <div class="lesson-sessions__item__content">
                                    <p><strong>Congratulations!</strong> You’ve made it to the end of the Masterclass!</p>
                                    <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Consectetur dicta eligendi eos iste quam quisquam.</p>
                                    <p class="margin">Lorem ipsum dolor sit amet, consectetur adipisicing elit.</p>
                                    <a target="_blank" href="{{ route('single.lesson.assessment.results', $lesson->slug) }}" data-user="{{ \Auth::user()->id }}" data-url="{{ route('single.lesson.assessment.check') }}" data-test="{{ $lesson->q_answered->assessment_id }}" data-popup="{{ route('lesson.testPopup', $lesson->id) }}" class="session-single__content-learn__default-btn-link js-retake-assessment">Re-take the assessment</a>
                                </div>
                            </div>
                        @endif
                    @else
                        <div class="lesson-sessions__item lesson-sessions__item--bonus js-bonus">
                            <p><strong>CONGRATULATIONS!</strong> You’ve just completed the FINAL Core Lesson in the ASK Method Masterclass!! Well done!</p>

                            <p>Now the important question is... What do you need next?  We’d love to support you as you grow your business using the ASK Method. </p>

                            <p>Tell us which one of the 5 ways to scale your income you are MOST excited about implementing in YOUR business and you will unlock your special, custom Easter Egg content.</p>

                            <p>I am most interested in learning how to scale my ASK Method Segmentation Funnel using:</p>

                            <form method="post" class="js-lesson-answer-question" action="{{ route('lesson.answerQuestion', $lesson->id) }}">
                                {{ csrf_field() }}
                                @foreach($lesson->questions as $question)
                                    <div class="lesson__question-radio">
                                        <input type="radio" id="question-{{ $question->id }}" name="question" value="{{ $question->id }}" />
                                        <label for="question-{{ $question->id }}"><strong>{{ $question->video_title }}</strong> - {{ $question->question }}</label>
                                    </div>
                                @endforeach
                                <input type="submit" value="Next">
                            </form>
                        </div>
                    @endif
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
