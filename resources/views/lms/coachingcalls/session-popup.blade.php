<div class="session-single__video-coaching" data-session="{{ $coaching_call->id }}" data-video="{{ $coaching_call->video_url }}">
     <div class="wistia_responsive_padding">
         <div class="wistia_responsive_wrapper">
             <div class="wistia_embed wistia_async_{{ $coaching_call->video_url }}"></div>
         </div>
     </div>
</div>

<div class="session-single__inner">
    <div class="session-single__content-main">        
        <h2>{{ $coaching_call->title }}</h2>
        <p>{!! $coaching_call->description !!}</p>
        {{-- <hr> --}}
    </div>
    
    @if(!empty($coaching_call->learn_more) || !empty($coaching_call->bucket_url)) 
        <div class="session-single__content-learn grid--flex flex--space-between">
            @if(!empty($coaching_call->learn_more))
                <div class="session-single__content-learn__links">
                    <h3>Learn More</h3>

                    {!! $coaching_call->learn_more !!}
                </div>
            @endif

            @if(!empty($coaching_call->bucket_url))
                <div class="session-single__content-learn__links">
                    <h3>Try in to BUCKET.IO</h3>

                    <a class="session-single__content-learn__bucket-link" href="{{ $coaching_call->bucket_url }}" target="_blank">Take Me There</a>
                </div>
            @endif
        </div>
    @endif
</div>
