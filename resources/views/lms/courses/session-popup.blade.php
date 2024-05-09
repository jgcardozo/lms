<div class="session-single__video" data-videotype="{{ $session->video_type->id }}" data-session="{{ $session->id }}"
    data-video="{{ $session->video_url }}" data-progress="{{ $videoprogress }}"
    data-route="{{ route('session.videoprogress', $session->id) }}">

    <div class="wistia_responsive_padding">
        <div class="wistia_responsive_wrapper">
            @php 
            if ($session->isLockedAtSchedule($session->id,$session->course->id,"Video")){
                $session->video_url = "2jjziapc0i"; //video-placeHolder instead of deActivating
            } 
                //  $session->refresh();
              //  dd($session);  
                //  2jjziapc0i  video placeholder    , real = "ji51hobnbq"  
            @endphp          
            @include('lms.components.video', ['model' => $session])
        </div>
        
{{--         @if ($session->isLockedAtSchedule($session->id,$session->course->id,"Video"))
          <div class="session_video-overlay"></div> 
        @endif  --}}
           @php
                    //dd("need sessionid for resource",$session->id);
                    //dd($session->isLockedAtSchedule($session->id,$session->course->id,"Video"));
                    //dd($session->isLockedAtSchedule($session->id,$session->course->id,"Learn"));
                //dd($resource->isLockedAtSchedule($session->id,$session->course->id)); //juanUpdate aca voy testing                      
            @endphp
        
    </div>
</div>

<div class="session-single__inner">
    <div class="session-single__content-main">
        @if (!empty($session->lesson))
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

             

    @if (!empty($session->learn_more) || !empty($session->bucket_url))
        <div class="session-single__content-learn grid--flex flex--space-between">
            @if (!empty($session->learn_more))
                <div class="session-single__content-learn__links">
                    <h3>Learn More</h3>
                    @if ($session->isLockedAtSchedule($session->id,$session->course->id,"Learn"))
                        <img src="{{asset('/images/icons/icon--lock.svg')}}" alt="Learn More is Locked"/>
                        {{-- <span style="color:#8f8e8e; text:center;">Locks in 3 days</span> --}}  
                    @else
                        {!! $session->learn_more !!}              
                    @endif       
                </div>
            @endif

            @if (!empty($session->bucket_url))
                <div class="session-single__content-learn__links">
                    <h3>Try it in BUCKET.IO</h3>

                    <a class="session-single__content-learn__bucket-link" href="{{ $session->bucket_url }}"
                        target="_blank">Take Me There</a>
                </div>
            @endif
        </div>
        <hr>
    @endif


    @if (!$session->resources->isEmpty())
        <div class="session-single__content-resources">
            <div class="session-single__content-resources--main grid--flex flex--space-between">
                <h3>Resources ({{ count($session->resources) }})</h3>
            </div>

            <div class="session-single__content-resources--links">
                <ul class="list--unstyled">
                    @foreach ($session->resources as $resource)
                     
                        <li>
                           
                            <div
                                class="session-single__content-resource--item grid--flex flex--space-between flex--align-center">
                                <div class="session-single__content-resource--name">
                                    {{ $resource->title }}
                                </div>

                                <div class="session-single__content-resource--info grid--flex">
                                    <div
                                        class="session-single__content-resource--type session-single__content-resource--type-{{ $resource->file_extension }}">
                                        {!! $resource->file_extension !!}
                                    </div>

                                    <div class="session-single__content-resource--file">
                                        {!! $resource->short_filename !!}
                                    </div>

                                    <div class="session-single__content-resource--file-size">
                                        {{ $resource->file_size_mb }}
                                    </div>

                                    <div class="session-single__content-resource--file-download">
                                            @if (!$resource->isLockedAtSchedule($session->id,$session->course->id))
                                                <a href="{{ $resource->file }}" target="_blank">Download</a>
                                            @else
                                                <span style="color:#8f8e8e;"><img src="{{asset('/images/icons/icon--lock.svg')}}" alt="Resource is Locked"/></span>
                                            @endif
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

    @if ($session->isCompleteVideoFeatureOn())
        @if ($session->is_completed)
            <div class="session-single__completed session-single__completed--mark-complete">
                <span class="course-progress__bar course-progress__bar--completed"></span> Completed
            </div>
        @else
            <div class="course-progress session-single__completed session-single__completed--mark-complete js-complete-session"
                data-complete="{{ route('session.completed', $session->id) }}"
                style="{{ !$session->isCourseMustWatch() || $videoprogress >= 80 || is_role_admin() ? '' : 'display: none' }}">
                <span class="course-progress__bar"></span> Mark as completed
            </div>
            <p style="{{ !$session->isCourseMustWatch() ? 'display:none;' : '' }}">Note: You have to watch 80% of the
                video before you can mark it as complete.</p>
        @endif
    @endif
</div>
