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
                <div class="course-single__content-wrap grid--flex flex--space-between">
                    <div class="single-header-block">
                        <div class="single-header-block__step-back">
                            <a href="{{ route('single.course', $course->slug) }}">
                                Back to <strong>{!! $course->title !!}</strong>
                            </a>
                        </div>
                        
                        <h2 class="single-header-block__title">{!! $course->title !!}</h2>
                        <p class="single-header-block__content">{{ $course->short_description }}</p>
                        <div class="single-header-block__separator"></div>
                        <div class="single-header-block__content single-header-block__content--small">
                            {!! $course->description !!}
                        </div>
                    </div>

                    <div class="single-header-video">
                        <div class="wistia_responsive_padding">
                            <div class="wistia_responsive_wrapper">
                                @include('lms.components.video', ['model' => $course])
                            </div>
                        </div>
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
                            <div class="course-starter__video grid--flex" @if($video->featured_image) style="background-image: url({{ $video->featured_image_url }});" @endif>
                                <a href="#" data-href="{{ route('session.show', $video->id) }}" class="block__link js-open-session"></a>
                            </div>

                            <div class="course-starter__content grid--flex flex--space-between flex--align-center">
                                <div class="course-starter__content--left">
                                    <h2 class="course-starter__item--title">{{ $video->title }}</h2>

                                    <p>{!! truncate_string($video->description) !!}</p>
                                </div>

                                <div class="course-starter__content--right">
                                    @if($video->is_completed)
                                        <div class="course-progress course-progress--completed">Completed <span class="course-progress__bar course-progress__bar--completed"></span></div>                                    
                                    @elseif($video->is_date_locked)
                                        <div class="course-progress" data-date=" until {{ date('d-m-Y', strtotime($video->getDate('lock_date'))) }}">
                                            Unlocks {{ Auth::user()->UnlockDate($video) }}
                                        </div>
                                    @else
                                        <div style="{{ $video->video_progress >= 80 || is_role_admin() ? '' : 'display: none;' }}" class="course-progress" data-complete="{{ route('session.completed', $video->id) }}">Mark as completed <span class="course-progress__bar"></span></div>
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

             <div class="session-single__content-ajax">

             </div>

         </div>
     </div>

@endsection
