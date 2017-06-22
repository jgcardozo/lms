<div class="session-single__student" style="display: none; text-align: center">
    <img src="{{ URL::to('/') }}/images/student.png" />
</div>

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
    
    @if(!empty($video->outer_url))
        <div class="session-single__content-learn js-end-course-outer-url">
            <div class="session-single__content-learn__links">
                <h3>The End of the Course</h3>

                <a href="{{ $video->outer_url }}" class="session-single__content-learn__default-btn-link" target="_blank">Yes, I Want To Know More!</a>
            </div>
        </div>
    @endif

    <div class="session-single__content-learn js-assessment-link" style="display: none">
        <div class="session-single__content-learn__links session-single__content-learn__links--full">
            <a target="_blank" href="{{ route('single.lesson.assessment.results', $video->lesson->slug) }}" data-user="{{ \Auth::user()->id }}" data-url="{{ route('single.lesson.assessment.check') }}" data-test="{{ $video->assessment_id }}" class="session-single__content-learn__default-btn-link" style="width: 100%">Take the assessment to see how much you've learned</a>
        </div>
    </div>
</div>

<div class="session-single__content__quiz js-assessment" style="display: none">
    <iframe
            src="https://www.classmarker.com/online-test/start?quiz={{ $video->assessment_embed_id }}
                    &cm_fn={{ \Auth::user()->profile && \Auth::user()->profile->first_name ? \Auth::user()->profile->first_name : '' }}
                    &cm_ln={{ \Auth::user()->profile && \Auth::user()->profile->last_name ? \Auth::user()->profile->last_name : '' }}
                    &cm_e={{ \Auth::user()->email }}
                    &cm_user_id={{ \Auth::user()->id }}"
            frameborder="0" style="width:100%;display: block; margin: 0 auto;" height="800">
    </iframe>
</div>
