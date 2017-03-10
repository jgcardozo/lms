@extends('layouts.app')

@section('title', $module->title)

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

                        <h2 class="single-header-block__title">{{ $module->title }}</h2>
                        <div class="single-header-block__content single-header-block__content--small">
                             {!! $module->description !!}
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
            <div class="module-lessons">
                <h2 class="module-lessons__title">Lessons</h2>

                <div class="module-lessons__list">
                    @foreach($module->lessons as $key => $lesson)
                        <div id="lesson-{{ $lesson->id }}" class="module-lessons__item grid--flex flex--space-between lesson {{ ($key % 3) == 0 ? 'lesson--first' : '' }}">
                            <div class="lesson-sessions__video grid--flex">
                                <a href="{{ route('single.lesson', $lesson->slug) }}" class="block__link"></a>
                            </div>

                            <div class="lesson-sessions__content grid--flex flex--space-between flex--align-center">
                                <div class="lesson-sessions__content--left">
                                    <h2 class="lesson-sessions__item--title">{{ $lesson->title }}</h2>
                                    <?php 
                                        //Get 2 sentences from description
                                        $strArray = explode('.', $lesson->description);
                                    ?>
                                    {!! $strArray[0] . '. ' . $strArray[1] . '.' !!}
                                </div>
                                
                                <div class="lesson-sessions__content--right">
                                    @if($lesson->is_completed)
                                        <span class="completed">Completed</span>
                                    @else
                                        <span class="mark-completed">Mark as completed</span>
                                    @endif

                                    @if($lesson->is_date_locked)
                                        <div class="locked" data-date=" until {{ date('d-m-Y', strtotime($lesson->lock_date)) }}"></div>
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