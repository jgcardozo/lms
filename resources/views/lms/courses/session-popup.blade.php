<div class="session-single__video" data-session="{{ $session->id }}" data-video="{{ $session->video_url }}" data-progress="{{ $videoprogress }}" data-route="{{ route('session.videoprogress', $session->id) }}">
     <div class="wistia_responsive_padding">
         <div class="wistia_responsive_wrapper">
             <div class="wistia_embed wistia_async_{{ $session->video_url }}"></div>
         </div>
     </div>
</div>

<div class="session-single__inner">
    <div class="session-single__content-main">
        @if(!empty($session->lesson))
            <h5>Module: {{ $session->lesson->module->title }} / {{ $session->lesson->title }}</h5>
            <h2>{!! $session->title !!}</h2>
            <p>{!! $session->description !!}</p>
        @else
            <h5>Course: {!! $session->course->title !!}</h5>
            <h2>{!! $session->title !!}</h2>
            <p>{!! $session->description !!}</p>
        @endif

        <hr>
    </div>
    
    @if(!empty($session->learn_more) || !empty($session->bucket_url)) 
        <div class="session-single__content-learn grid--flex flex--space-between">
            @if(!empty($session->learn_more))
                <div class="session-single__content-learn__links">
                    <h3>Learn More</h3>

                    {!! $session->learn_more !!}
                </div>
            @endif

            @if(!empty($session->bucket_url))
                <div class="session-single__content-learn__links">
                    <h3>Try in to BUCKET.IO</h3>

                    <a class="session-single__content-learn__bucket-link" href="{{ $session->bucket_url }}" target="_blank">Take Me There</a>
                </div>
            @endif
        </div>
        <hr>
    @endif

    @if(!$session->resources->isEmpty())        
        <div class="session-single__content-resources">
            <div class="session-single__content-resources--main grid--flex flex--space-between">
                <h3>Resources ({{ count($session->resources) }})</h3>
                {{-- <a class="session-single__content-resources--download-all" href="#">Download All</a> --}}
            </div>

            <div class="session-single__content-resources--links">
                <ul class="list--unstyled">
                    @foreach($session->resources as $resource)
                        <li>
                            <div class="session-single__content-resource--item grid--flex flex--space-between flex--align-center">
                                <div class="session-single__content-resource--name">
                                    {{ $resource->title }}
                                </div>

                                <div class="session-single__content-resource--info grid--flex">
                                    <div class="session-single__content-resource--type session-single__content-resource--type-{{ $resource->file_extension }}">
                                        {{ $resource->file_extension }}
                                    </div>

                                    <div class="session-single__content-resource--file">
                                        {{ $resource->short_filename }}
                                    </div>

                                    <div class="session-single__content-resource--file-size">
                                        {{ $resource->file_size_mb }}
                                    </div>

                                    <div class="session-single__content-resource--file-download">
                                        <a href="{{ $resource->file }}" target="_blank">Download</a>
                                    </div>
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <hr>

    <div class="course-progress session-single__completed session-single__completed--mark-complete js-complete-session" data-complete="{{ route('session.completed', $session->slug) }}" style="{{ $videoprogress >= 80 ? '' : 'display: none' }}">
        <span class="course-progress__bar"></span> Mark as completed
    </div>
    <p>Note: You have to watch 80% of the video before you can mark it as complete.</p>
</div>
