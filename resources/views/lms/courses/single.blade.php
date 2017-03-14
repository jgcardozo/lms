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
                <div class="grid--flex flex--space-between">
                    @if(!$starterSeen)
                        <div class="course-reminder__content">
                            <p class="course-reminder__blurb">Hi there</p>
                            <h2 class="course-reminder__title">Welcome to {{ $course->title }}</h2>
                            <p>{{ $course->short_description }}</p>
                        </div>

                        <div class="grid--flex flex--align-center">
                            <a href="{{ route('single.course.starter', $course->slug) }}" class="course-reminder__link">Watch videos</a>
                        </div>
                    @elseif(!empty($nextSession))
                        <div class="course-reminder__content">
                            <p class="course-reminder__blurb">Last Session</p>
                            <h2 class="course-reminder__title">Welcome to {{ $nextSession->title }}</h2>
                            <p>{{ strip_tags($nextSession->description) }}</p>
                        </div>

                        <div class="grid--flex flex--align-center">
                            <a href="{{ route('single.lesson', $nextSession->lesson->slug) }}" class="course-reminder__link">Resume Lesson ></a>
                        </div>                    
                    @else
                        <div class="course-reminder__content">
                            <p class="course-reminder__blurb">Congrats</p>
                            <h2 class="course-reminder__title">You watched them all :)</h2>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="grid grid--w950">
            <div class="course-modules">
                <h2 class="course-modules__title">{{ $course->module_group_title }}</h2>

                <div class="grid--flex course-modules__list">
                    @foreach($course->modules as $key => $module)
                        <div id="module-{{ $module->id }}" class="module grid--flex {{ ($key % 3) == 0 ? 'module--first' : '' }}">
                            <div class="module__component grid--flex flex--column">
                                @if($module->is_locked)
                                    <div class="module__locked"
                                        @if($module->is_date_locked)
                                            data-date=" until {{ date('d-m-Y', strtotime($module->lock_date)) }}"
                                        @endif
                                    ><i class="icon--lock"></i></div>
                                @endif

                                <div class="module__featured-image">
                                    @if($module->is_completed)
                                        <span class="completed">Completed</span>
                                    @endif
                                </div>
                                
                                <div class="module__content">
                                    <h2 class="module__title">{{ $module->title }}</h2>
                                    <?php 
                                        //Get 80 characters from description
                                        $strArray = substr(strip_tags($module->description),0,80).'...';
                                    ?>
                                    <p>{{ $strArray }}</p>

                                    @if($module->is_locked)
                                        <a href="javascript:;" class="module__link">Go To Lesson</a>
                                    @else
                                        <a href="{{ route('single.module', $module->slug) }}" class="module__link">Go To Lesson</a>
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