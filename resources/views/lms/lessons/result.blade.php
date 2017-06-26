@extends('layouts.app')

@section('title', $lesson->title)

@section('scripts_before')
    <script src="//fast.wistia.com/assets/external/E-v1.js" async></script>
@endsection

@section('content')
    <main>
        <div class="grid grid--w950 course-single__content" style="padding-bottom: 0">
            @if($score)
                <div class="lesson-result__score">
                    <img src="{{ URL::to('/') }}/images/icons/student_happy.svg" />

                    <h1>Congratulations!!</h1>


                        <div class="lesson-result__score__num">
                            <p>Your score is:</p>
                            <h4>{{ $score }}%</h4>
                        </div>
                </div>

                <br />

                <div class="lesson-result__main-video">
                    <div class="wistia_responsive_padding">
                        <div class="wistia_responsive_wrapper">
                            <div class="wistia_embed wistia_async_i20q21p026"></div>
                        </div>
                    </div>

                    <h4>You’ve passed with flying colors and you did AWESOME!</h4>

                    <p>I’m SO excited to see what you will achieve with the ASK Method!</p>

                    <p>And to celebrate, you’ll be receiving your certificate (and a few other goodies!!) to acknowledge you for your MAJOR accomplishment.</p>
                    <p>Congratulations, you are our newest fully fledged graduate of the ASK Method Masterclass!</p>
                    <p>Thank you for being a part of this very special community.</p>
                    <p>And as a special bonus right now - you have just unlocked ALL the final Easter Eggs for Module 3 (see below)!</p>
                    <p>Let us know how we can support you moving forward and remember, you don’t have to get it perfect… you just have to get it going!</p>
                    </p>
                </div>

                <div class="lesson-result__videos grid--flex">
                    @foreach($lesson->questions as $question)
                        <div class="lesson-result__video">
                            <div class="lesson-result__video__image">
                                <img src="{{ $question->featured_image_url }}" />
                                <a href="{{ route('single.lesson.viewResultsVideoPopup', $question->id) }}" class="lesson-result__video__image__play js-play-question-video"></a>
                            </div>

                            <h5>{{ $question->video_title }}</h5>
                            <p>{!! truncate_string($question->description, 10) !!}</p>
                        </div>
                    @endforeach
                </div>

                <div class="lesson-result__cta-wrap grid--flex flex--space-between">
                    <div class="lesson-result__cta">
                        <h2>Want help growing your business and scaling your income?</h2>

                        <p>Apply Now for the ASK Method Coaching & Mentorship program!</p>
                        <a href="https://get.askmethod.com/ask-coaching-application-ns93merq/" target="_blank" class="btn">Yes, I Want To Know More!</a>
                    </div>
                    <div class="lesson-result__cta">
                        <h2>Want to implement the ASK Method for other businesses?</h2>

                        <p>Apply Now for the ASK Method Certification Program!</p>
                        <a href="https://get.askmethod.com/ask-certification-application-u8shba3a/" target="_blank" class="btn">Yes, I Want To Know More!</a>
                    </div>
                </div>
            @else
                <div class="lesson-result__score">
                    <img src="{{ URL::to('/') }}/images/icons/student_sad.svg" />

                    <h1>Whoops!!</h1>


                    <div class="lesson-result__score__num">
                        <p>Looks like you might need to do a little “refresher” :-)</p>
                        <br/>
                        <a target="_blank" href="{{ route('single.lesson.assessment.results', $lesson->slug) }}" data-user="{{ \Auth::user()->id }}" data-url="{{ route('single.lesson.assessment.check') }}" data-test="{{ $lesson->q_answered->assessment_id }}" data-popup="{{ route('lesson.testPopup', $lesson->id) }}" class="session-single__content-learn__default-btn-link js-retake-assessment">Go Here To Take The Assessment Again</a>
                    </div>
                </div>
            @endif
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