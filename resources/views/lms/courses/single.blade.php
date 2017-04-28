@extends('layouts.app')

@section('title', $course->title)

@section('scripts_before')
    <script src="//fast.wistia.com/assets/external/E-v1.js" async></script>
@endsection

@section('content')
    <main>
        <div class="grid grid--full course-single" @if($course->featured_image) style="background-image: url({{ $course->getFeaturedImageUrlAttribute() }});" @endif>
            <div class="course-single__overlay"></div>

            <div class="grid grid--w950 course-single__content">
                <div class="course-single__content-wrap grid--flex flex--space-between">
                    <div class="single-header-block">
                        <h2 class="single-header-block__title ucase">{!! bold_first_word($course->title) !!}</h2>
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
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid--w950 course-reminder">
            <div class="course-reminder__block">
                <div class="course-reminder__block-wrap grid--flex flex--space-between">
                    @if($starterSeen === false)
                        <div class="course-reminder__content">
                            <h2 class="course-reminder__title">Getting Started: Begin by Watching these Intro Videos First</h2>
                            <p>How to find your way around, access important resources, and win great prizes!</p>
                        </div>

                        <div class="grid--flex flex--align-center">
                            <a href="{{ route('single.course.starter', $course->slug) }}" class="course-reminder__link {{ !empty($popupBefore) ? 'js-open-surveyPopup' : '' }}">Watch videos</a>
                        </div>
                    @elseif(!empty($nextSession) && $nextSession !== true)
                        <div class="course-reminder__content">
                            <p class="course-reminder__blurb">Next Session</p>
                            <h2 class="course-reminder__title">Welcome to {{ $nextSession->title }}</h2>
                            <p>{{ truncate_string($nextSession->description) }}</p>
                        </div>

                        <?php
                            $t = false;
                            foreach($course->modules as $key => $module)
                            {
                                if($module->is_locked)
                                    continue;

                                $t = true;
                            }

                            if($t)
                            {
                                ?>
                                <div class="grid--flex flex--align-center">
                                    <a href="{{ route('single.lesson', $nextSession->lesson->slug) }}" class="course-reminder__link">Resume Lesson</a>
                                </div>
                                <?php
                            }else{
                                ?>
                                <div class="grid--flex flex--align-center">
                                    <a href="#" class="course-reminder__link">Unlocks soon</a>
                                </div>
                                <?php
                            }
                        ?>
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
                                    <div class="module__locked">

                                    @if($module->is_date_locked)
                                            {{-- <p>Unlocks {{ date('d-m-Y', strtotime($module->lock_date)) }}</p> --}}
                                    @endif
                                    <i class="icon--lock"></i></div>
                                @endif

                                <div class="module__featured-image" @if($module->featured_image) style="background-image: url({{ $module->getFeaturedImageUrlAttribute() }});" @endif>
                                    @if(! $module->is_locked)
                                        <div class="module__active" data-percentage="{!! $module->getProgressPercentage() / 100 !!}"></div>
                                    @endif

                                    @if($module->is_completed)
                                        <div class="module__completed">Completed</div>
                                    @endif
                                </div>
                                
                                <div class="module__content">
                                    <h2 class="module__title">{{ $module->title }}</h2>

                                    <p>{{ truncate_string($module->description) }}</p>

                                    @if($module->is_locked)
                                        <a href="javascript:;" class="module__link">Go To Lesson</a>
                                    @else
                                        <a href="{{ route('single.module', $module->slug) }}" class="module__link">Go To Module</a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        @if(!empty($popupBefore))
            @include('lms.survey')
        @endif
    </main>
@endsection