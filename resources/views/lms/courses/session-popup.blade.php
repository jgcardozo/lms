<div class="session-single__video" data-session="{{ $session->id }}" data-video="{{ $session->video_url }}" data-progress="{{ $videoprogress }}" data-route="{{ route('session.videoprogress', $session->id) }}">
     <div class="wistia_responsive_padding">
         <div class="wistia_responsive_wrapper">
             <div class="wistia_embed wistia_async_{{ $session->video_url }}"></div>
         </div>
     </div>
</div>

<div class="session-single__inner">
    <div class="session-single__content-main">
        <h5>Module: {{ $session->lesson->module->title }} / {{ $session->lesson->title }}</h5>
        <h2>{{ $session->title }}</h2>
        <p>{!! $session->description !!}</p>

        <hr>
    </div>

    <div class="session-single__content-learn grid--flex flex--space-between">
        <div class="session-single__content-learn__links">
            <h3>Learn More</h3>

            <ul class="list--unstyled">
                <li><a href="#">Donec faucibius saggitis posuere. Macenas consectetur</a></li>
                <li><a href="#">Donec faucibius saggitis.</a></li>
                <li><a href="#">Donec faucibius saggitis posuere.</a></li>
            </ul>
        </div>

        <div class="session-single__content-learn__links">
            <h3>Sign in to BUCKET.IO</h3>

            <a class="session-single__content-learn__bucket-link" href="#">Take Me There</a>
        </div>                    
    </div>

    <hr>

    <div class="session-single__content-resources">
        <div class="session-single__content-resources--main grid--flex flex--space-between">
            <h3>Files ({{ count($session->resources) }})</h3>
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

    <hr>

    <div class="session-single__completed session-single__completed--mark-complete js-complete-session" style="{{ $videoprogress >= 80 ? '' : 'display: none' }}">
        <span class="course-progress__bar"></span> Mark as completed
    </div>
</div>
