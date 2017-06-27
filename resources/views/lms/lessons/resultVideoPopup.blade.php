<div class="session-single__video">
    <div class="wistia_responsive_padding">
        <div class="wistia_responsive_wrapper">
            <div class="wistia_embed wistia_async_{{ $video->video_url }}"></div>
        </div>
    </div>

    <div class="session-single__inner question-popup">
        <div class="session-single__content-main">
            <h2>{!! $video->video_title !!}</h2>
            {!! $video->description !!}
        </div>

        <div class="session-single__content-learn">
            <div class="session-single__content-learn__links">
                <a href="{{ $video->outer_url }}" class="session-single__content-learn__default-btn-link" target="_blank">Yes, I Want To Know More!</a>
            </div>
        </div>
    </div>
</div>