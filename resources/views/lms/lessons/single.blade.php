@extends('layouts.app')

@section('title', $lesson->title)

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
                            <a href="{{ route('single.module', $lesson->module->slug) }}">
                                Back to <strong>{{ $lesson->module->title }}</strong>
                            </a>
                        </div>

                        <h2 class="single-header-block__title">{{ $lesson->title }}</h2>
                        <div class="single-header-block__content single-header-block__content--small">
                            {!! $lesson->description !!}
                        </div>
                    </div>

                    <div class="single-header-video">
                         <div class="wistia_responsive_padding">
                             <div class="wistia_responsive_wrapper">
                                 <div class="wistia_embed wistia_async_{{ $lesson->video_url }}"></div>
                             </div>
                         </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="grid grid--w950">
            <div class="lesson-sessions">
                <h2 class="lesson-sessions__title">Sessions</h2>

                <div class="lesson-sessions__list">
                    @foreach($lesson->sessions as $key => $session)
                        <div id="session-{{ $session->id }}" class="lesson-sessions__item grid--flex flex--space-between">
                            @if($session->is_date_locked)
                                <div class="lesson-sessions__item--locked-overlay"></div>
                            @endif
                            <div class="lesson-sessions__video grid--flex">
                                @if($session->is_date_locked)
                                    <div class="course-progress grid--flex flex--align-center flex--just-center">
                                        <span class="course-progress__bar course-progress__bar--locked"></span>
                                    </div>
                                @else
                                    <a href="{{ route('session.completed', $session->slug) }}" class="block__link js-open-session"></a>
                                @endif
                            </div>

                            <div class="lesson-sessions__content grid--flex flex--space-between flex--align-center">
                                <div class="lesson-sessions__content--left">
                                    <h2 class="lesson-sessions__item--title">{{ $session->title }}</h2>

                                    <p>{{ truncate_string($session->description) }}</p>
                                </div>

                                <div class="lesson-sessions__content--right">
                                    @if($session->is_completed)
                                        <div class="course-progress course-progress--completed">Completed <span class="course-progress__bar course-progress__bar--completed"></span></div>                                    
                                    @elseif($session->is_date_locked)
                                        <div class="course-progress" data-date=" until {{ date('d-m-Y', strtotime($session->lock_date)) }}">
                                            Unlocks {{ date('d-m-Y', strtotime($session->lock_date)) }} 
                                        </div>
                                    @else
                                        <div class="course-progress" data-complete="{{ route('session.completed', $session->slug) }}">Mark as completed <span class="course-progress__bar"></span></div>
                                    @endif
                                </div>                                
                            </div>                            
                        </div>
                    @endforeach
                </div>

                @if($lesson->is_completed && $lesson->has_bonus)
                    @if(!$lesson->is_fb_posted)
                        <div class="lesson-sessions__item lesson-sessions__item--bonus">
                            <p>Awesome! You have finished this Lesson. Time to unlock a hidden bonus by answering a simple question: <strong>What was your biggest takeaway from this module?</strong></p>

                            <form>
                                <textarea class="js-count-chars" data-chars="bonus" name="facebook_post" maxlength="200"></textarea>

                                <div class="form-group grid--flex flex--end flex--align-center">
                                    <div class="chars-count" data-chars="bonus"><span>200</span> Left</div>
                                    <input type="submit" value="Send">
                                </div>
                            </form>
                        </div>
                    @else
                        <div class="lesson-sessions__item lesson-sessions__item--bonus">
                            <p>Thank you! Here’s your ultra secret bonus content.</p>

                            <div class="grid--flex flex--space-between">
                                <div class="lesson-sessions__item--bonus-video">
                                    <script src="//fast.wistia.com/assets/external/E-v1.js" async></script>
                                    <div class="wistia_responsive_padding" style="padding:56.67% 0 0 0;position:relative;">
                                        <div class="wistia_responsive_wrapper" style="height:100%;left:0;position:absolute;top:0;width:100%;">
                                            <div class="wistia_embed wistia_async_eeww27k7yw" style="width:100%;height:100%;"></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="lesson-sessions__item--bonus-content">
                                    <h3>Easter Egg Video</h3>
                                    <h5>Duration 12 min</h5>
                                    <p>Donec faucibus sagittis posuere. Maecenas consectetur vel eros elementum ultricies. Pellentesque turpis lorem, tincidunt accumsan magna vel, iaculis convallis sapien. Suspendisse vestibulum varius magna, nec venenatis est cursus nec. Sed efficitur sodales diam, a faucibus orci fringilla at. In quis nisl mattis, placerat neque convallis.</p>
                                </div>
                            </div>
                        </div>
                    @endif
                @endif
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
