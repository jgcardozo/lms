<div class="easteregg-step easteregg-step-1">
    <div class="session-single__video">
        <div class="wistia_responsive_padding">
            <div class="wistia_responsive_wrapper">
                <div class="wistia_embed wistia_async_{{ $video->video_url }}"></div>
            </div>
        </div>
    </div>

    <div class="session-single__inner question-popup">
        <div class="session-single__content-main">
            <h2>{!! $video->video_title !!}</h2>
            {!! $video->description !!}
            <hr>
        </div>

        <div class="session-single__content-learn">
            <div class="session-single__content-learn__links">
                <a href="{{ $video->outer_url }}" class="session-single__content-learn__default-btn-link" target="_blank">Yes, I Want To Know More!</a>
            </div>
        </div>
    </div>
</div>

<div class="easteregg-step easteregg-step-2">
    <div class="session-single__student" style="text-align: center">
        <div class="lesson-result__score" style="padding-top: 5rem;">
            <img src="{{ URL::to('/') }}/images/icons/student_happy.svg" style="max-width: 15rem" />

            <h1>Congratulations!!</h1>

            <div class="lesson-result__score__num">
                <p>You completed the course!</p>
            </div>
        </div>
    </div>

    <div class="session-single__video">
        <div class="wistia_responsive_padding">
            <div class="wistia_responsive_wrapper">
                <div class="wistia_embed wistia_async_ylziue4yrq"></div>
            </div>
        </div>
    </div>

    <div class="session-single__inner question-popup">
        <div class="session-single__content-main">
            <h2>YOU DID IT!!</h2>
            <p>You’ve made it to the end of the Masterclass!</p>
            <p>But there’s one <strong>final</strong> step. Just like any great course, you have your final exam.</p>

            <p>Now, it’s not a super hard final exam! It’s actually a pretty fun assessment. This is an awesome way for you to see how much you’ve learned about the ASK Method and growing your business.</p>

            <p>Plus, I’ve got a surprise for you for completing the course, which you’ll see once you complete your course assessment.</p>

            <p>So, go ahead and do that now.</p>
            <hr>
        </div>

        @if(!empty($video->outer_url))
            <div class="session-single__content-learn js-end-course-outer-url">
                <div class="session-single__content-learn__links session-single__content-learn__links--full">
                    <a target="_blank" href="{{ route('single.lesson.assessment.results', $video->lesson->slug) }}" data-user="{{ \Auth::user()->id }}" data-url="{{ route('single.lesson.assessment.check') }}" data-test="{{ $video->assessment_id }}" class="session-single__content-learn__default-btn-link js-assessment-link" style="width: 100%">Take the assessment to see how much you've learned</a>
                </div>
            </div>
        @endif
    </div>
</div>

<div class="easteregg-step easteregg-step-3 js-assessment">
    <iframe
            src="https://www.classmarker.com/online-test/start?quiz={{ $video->assessment_embed_id }}
                    &cm_fn={{ \Auth::user()->profile && \Auth::user()->profile->first_name ? \Auth::user()->profile->first_name : '' }}
                    &cm_ln={{ \Auth::user()->profile && \Auth::user()->profile->last_name ? \Auth::user()->profile->last_name : '' }}
                    &cm_e={{ \Auth::user()->email }}
                    &cm_user_id={{ \Auth::user()->id }}"
            frameborder="0" style="width:100%;display: block; margin: 0 auto;" height="800">
    </iframe>
</div>