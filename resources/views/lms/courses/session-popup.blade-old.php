<div class="session-single__video" data-videotype="{{ $session->video_type->id }}" data-session="{{ $session->id }}" data-video="{{ $session->video_url }}" data-progress="{{ $videoprogress }}" data-route="{{ route('session.videoprogress', $session->id) }}"
@if ($session->reveal('video_reveal_at')) style="display:none;" @endif    
>
     <div class="wistia_responsive_padding">
         <div class="wistia_responsive_wrapper">
             @include('lms.components.video', ['model' => $session])
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
                    <div @if ($session->reveal('learnmore_reveal_at')) style="display:none;" @endif>
                        {!! $session->learn_more !!}
                    </div>    
                </div>
            @endif

            @if(!empty($session->bucket_url))
                <div class="session-single__content-learn__links">
                    <h3>Try it in BUCKET.IO</h3>

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
                    @php
                      //dd($session->dateFromSchedule($resource->id))
                    @endphp
                  
                        <li @if ($session->dateFromSchedule($resource->id)) style="display:none;" @endif>
                            <div class="session-single__content-resource--item grid--flex flex--space-between flex--align-center">
                                <div class="session-single__content-resource--name">
                                    {{ $resource->title }}
                                </div>

                                <div class="session-single__content-resource--info grid--flex">
                                    <div class="session-single__content-resource--type session-single__content-resource--type-{{ $resource->file_extension }}">
                                        {!! $resource->file_extension !!}
                                    </div>

                                    <div class="session-single__content-resource--file">
                                        {!!  $resource->short_filename !!}
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

    @if($session->isCompleteVideoFeatureOn())
        @if($session->is_completed)
            <div class="session-single__completed session-single__completed--mark-complete">
                <span class="course-progress__bar course-progress__bar--completed"></span> Completed
            </div>
        @else
            <div class="course-progress session-single__completed session-single__completed--mark-complete js-complete-session"
                 data-complete="{{ route('session.completed', $session->id) }}"
                 style="{{ ! $session->isCourseMustWatch() || $videoprogress >= 80 || is_role_admin() ? '' : 'display: none' }}">
                <span class="course-progress__bar"></span> Mark as completed
            </div>
            <p style="{{ ! $session->isCourseMustWatch() ? 'display:none;' : '' }}">Note: You have to watch 80% of the video before you can mark it as complete.</p>
        @endif
    @endif
</div>
