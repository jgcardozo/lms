@extends('layouts.app')

@section('title', $course->title)

@section('content')
     <main>
        <div class="grid grid--full course-single">
            <div class="course-single__overlay"></div>

            <div class="grid grid--w950 course-single__content">
                <div class="grid--flex flex--space-between">
                    <div class="single-header-block">
                        <div class="single-header-block__step-back">
                            <a href="{{ route('single.course', $module->course->slug) }}">
                                Back to <strong>{{ $module->course->title }}</strong>
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
                         <script src="//fast.wistia.com/assets/external/E-v1.js" async></script>
                         <div class="wistia_responsive_padding" style="padding:56.67% 0 0 0;position:relative;">
                             <div class="wistia_responsive_wrapper" style="height:100%;left:0;position:absolute;top:0;width:100%;">
                                 <div class="wistia_embed wistia_async_gpc49zomb2" style="width:100%;height:100%;"></div>
                             </div>
                         </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="grid grid--w950">
            <div class="lesson-sessions">
                <h2 class="lesson-sessions__title">Getting started</h2>

                <div class="lesson-sessions__list">
                    @foreach($videos as $video)
                        <div id="session-{{ $video->id }}" class="lesson-sessions__item grid--flex flex--space-between">
                            <div class="lesson-sessions__video grid--flex">
                                <a href="{{ route('session.completed', $video->slug) }}" class="block__link"></a>
                            </div>

                            <div class="lesson-sessions__content grid--flex flex--space-between flex--align-center">
                                <div class="lesson-sessions__content--left">
                                    <h2 class="lesson-sessions__item--title">{{ $video->title }}</h2>
                                    <?php 
                                        //Get 2 sentences from description
                                        $strArray = explode('.', $video->description);
                                    ?>
                                    {!! $strArray[0] . '. ' . $strArray[1] . '.' !!}
                                </div>

                                <div class="lesson-sessions__content--right">
                                    @if($video->is_completed)
                                        <span class="completed">Completed</span>
                                    @else
                                        <span class="mark-completed">Mark as completed</span>
                                    @endif
                                </div>                                
                            </div>                            
                        </div>
                    @endforeach
                </div>
            </div>
        </div>        
    </main>
@endsection