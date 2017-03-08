@extends('layouts.app')

@section('title', $course->title)

@section('content')
    <main>
        <div class="grid grid--full course-single">
            <div class="course-single__overlay"></div>

            <div class="grid grid--w950 course-single__content">
                <div class="grid--flex flex--space-between">
                    <div class="single-header-block">
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

        <div class="grid grid--w950 course-reminder">
            <div class="course-reminder__block">
                @if(!$starterSeen)
                    <span><em>Hi there</em></span>
                    <p>Welcome to {{ $course->title }}</p>
                    <span>{{ $course->short_description }}</span>
                    <a href="{{ route('single.course.starter', $course->slug) }}" class="watch-them">Watch videos</a>
                @elseif(!empty($nextSession))
                    <span><em>Last Session</em></span>
                    <p>Welcome to {{ $nextSession->title }}</p>
                    <span>{{ strip_tags($nextSession->description) }}</span>
                    <a href="{{ route('single.lesson', $nextSession->lesson->slug) }}" class="watch-them">Resume</a>
                @else
                    <span><em>Congrats</em></span>
                    <p>You watch them all, go to hell</p>
                    <span></span>
                @endif
            </div>
        </div>

        <div class="grid grid--w950">
            <div class="course-modules">
                <h2 class="course-modules__title">{{ $course->module_group_title }}</h2>

                <div class="grid--flex course-modules__list">
                    @foreach($course->modules as $key => $module)
                        <div id="module-{{ $module->id }}" class="module {{ ($key % 3) == 0 ? 'module--first' : '' }}">

                            @if($module->is_locked)
                                <div class="locked"
                                    @if($module->is_date_locked)
                                        data-date=" until {{ date('d-m-Y', strtotime($module->lock_date)) }}"
                                    @endif
                                ></div>
                            @endif

                            <div class="module__featured-image">
                                @if($module->is_completed)
                                    <span class="completed">Completed</span>
                                @endif
                            </div>
                            
                            <div class="module__content">
                                <h2 class="module__title">{{ $module->title }}</h2>                                

                                {!! $module->description !!}

                                <a href="{{ route('single.module', $module->slug) }}" class="module__link">View module</a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </main>
@endsection