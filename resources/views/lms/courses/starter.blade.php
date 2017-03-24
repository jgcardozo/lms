@extends('layouts.app')

@section('title', $course->title)

@section('scripts_before')
    <script src="//fast.wistia.com/assets/external/E-v1.js" async></script>
@endsection

@section('content')
     <main>
        <div class="grid grid--full course-single">
            <div class="course-single__overlay"></div>

            <div class="grid grid--w950 course-single__content">
                <div class="grid--flex flex--space-between">
                    <div class="single-header-block">
                        <div class="single-header-block__step-back">
                            <a href="{{ route('single.course', $course->slug) }}">
                                Back to <strong>{{ $course->title }}</strong>
                            </a>
                        </div>
                        
                        <h2 class="single-header-block__title">{{ $course->title }}</h2>
                        <p class="single-header-block__content">{{ $course->short_description }}</p>
                        <div class="single-header-block__separator"></div>
                        <div class="single-header-block__content single-header-block__content--small">
                            {!! $course->description !!}
                        </div>
                    </div>

                    <div class="single-header-video">
                        <div class="wistia_responsive_padding">
                            <div class="wistia_responsive_wrapper">
                                <div class="wistia_embed wistia_async_{{ $course->video_url }}"></div>
                            </div>
                        </div>

                        <script>
                            window._wq = window._wq || [];

                            _wq.push({ id: "gpc49zomb2", onReady: function(video) {
                                var watchRule = parseInt(video.duration()*0.8);
                              
                                video.bind('secondchange', function(s) {
                                    if (s === watchRule) {                                 
                                        console.log("We just reached " + s + " seconds!");
                                    }

                                    if (video.secondsWatched() >= watchRule) {
                                        console.log("The video session can be completed!");
                                    }
                                });
                            }});

                            // var playedOnce = false;
                            // window._wq = window._wq || [];
                            // _wq.push({id: "gpc49zomb2", onReady: function(video) {
                            //     if (!playedOnce && /[&?]popoverAutoplay/i.test(location.href)) {
                            //       playedOnce = true;
                            //       video.popover.show()
                            //       video.play();
                            //     }
                            // }});

                        </script>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="grid grid--w950">
            <div class="course-starter">
                <h2 class="course-starter__title">Getting started</h2>

                <div class="course-starter__list">
                    @foreach($videos as $video)
                        <div id="session-{{ $video->id }}" class="course-starter__item grid--flex flex--space-between">
                            <div class="course-starter__video grid--flex">
                                <a href="{{ route('session.completed', $video->slug) }}" class="block__link js-open-session"></a>
                            </div>

                            <div class="course-starter__content grid--flex flex--space-between flex--align-center">
                                <div class="course-starter__content--left">
                                    <h2 class="course-starter__item--title">{{ $video->title }}</h2>

                                    <p>{{ truncate_string($video->description) }}</p>
                                </div>

                                <div class="course-starter__content--right">
                                    @if($video->is_completed)
                                        <div class="course-progress course-progress--completed">Completed <span class="course-progress__bar course-progress__bar--completed"></span></div>
                                    @else
                                        <div class="course-progress">Mark as completed <span class="course-progress__bar"></span></div>
                                    @endif
                                </div>                                
                            </div>                            
                        </div>
                    @endforeach
                </div>
            </div>
        </div>        
    </main>

    <div class="session-single">
        <div class="session-single__content">
            <div class="session-single__close"></div>

            @include('lms.courses.session-popup')

        </div>
    </div>

@endsection
