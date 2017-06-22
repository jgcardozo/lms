<header class="masthead">
    <div class="grid grid--full">
        <div class="grid--flex flex--space-between">
            <div class="mobile-menu__open"></div>
            <div class="masthead__left grid--flex">
                @if(changeHeader())                    
                    <div class="masthead__classes grid--flex flex--align-center">
                        <a class="masthead__classes-link masthead__classes-link--active js-header-classes" href="javascript:;" @if($progress_items->logo_image) style="background-image: url({{ $progress_items->getLogoImageUrlAttribute() }});" @endif>
                            {!! bold_first_word($progress_items->title) !!}
                        </a>
                        
                        <div class="masthead__classes-wrap">
                            <ul class="masthead__classes-list list--unstyled">
                                <li class="masthead__classes-list__item masthead__classes-list__item--locked">
                                    <a class="masthead__classes-link" href="{{ url('/') }}" style="background-image: url({{ asset('images/backtoallcourses.png') }});">Back to All Courses</a>
                                </li>
                                @foreach($courses as $course)
                                    @if($course->is_locked)
                                        <li class="masthead__classes-list__item masthead__classes-list__item--locked">
                                            @if($course->apply_now)
                                                <a class="masthead__classes-link masthead__classes-link--locked" href="{{ $course->apply_now }}" @if($course->logo_image) style="background-image: url({{ $course->getLogoImageUrlAttribute() }});" @endif target="_blank">{!! bold_first_word($course->title) !!}</a>
                                            @else
                                                <a class="masthead__classes-link masthead__classes-link--locked" href="javascript:;" @if($course->logo_image) style="background-image: url({{ $course->getLogoImageUrlAttribute() }});" @endif>{!! bold_first_word($course->title) !!}</a>
                                            @endif
                                        </li>
                                    @else
                                        <li class="masthead__classes-list__item">
                                            <a class="masthead__classes-link" href="{{ route('single.course', $course->slug) }}" @if($course->logo_image) style="background-image: url({{ $course->getLogoImageUrlAttribute() }});" @endif>{!! bold_first_word($course->title) !!}</a>
                                        </li>
                                    @endif
                                @endforeach
                            </ul>
                        </div>
                    </div>

                    <div class="masthead__main-links grid--flex">
                        <ul class="list--inline grid--flex">
                            @if(!$progress_items->starter_videos->isEmpty())
                                <li class="grid--flex"><a href="{{ route('single.course.starter', $progress_items->slug) }}" class="grid--flex flex--align-center {{ survey_check($progress_items) ? 'js-open-surveyPopup' : '' }}">Welcome</a></li>
                            @endif

                            @if(isset($progress_items))
                                @if($progress_items->training_fields)
                                    {{-- <li class="grid--flex"><a class="grid--flex flex--align-center @if(Route::currentRouteName() == 'single.course.training') active @endif" href="{{ route('single.course.training', $progress_items->slug) }}">Training</a></li> --}}
                                @endif

                                @if($progress_items->featured_coachingcall && !Auth::user()->hasTag($progress_items->cancel_tag))
                                    <li class="grid--flex"><a class="grid--flex flex--align-center @if(Route::currentRouteName() == 'single.course.coaching-call') active @endif" href="{{ route('single.course.coaching-call', $progress_items->slug) }}">Q&A Calls</a></li>
                                @endif

                                @if($progress_items->facebook_group_id && !Auth::user()->hasTag($progress_items->cancel_tag))
                                    <li class="grid--flex"><a class="grid--flex flex--align-center js-fb-group" href="{{ $progress_items->facebook_group_id }}" target="_blank"><i class="icon--facebook"></i> Facebook Group</a></li>
                                @endif
                            @endif
                            </ul>
                        </div>
                    @else
                        <div class="masthead__logo grid--flex flex--align-center">
                            <a class="masthead__logo-link" href="{{ url('/') }}">
                                {{ config('app.name', 'Laravel') }}
                            </a>
                        </div>
                    @endif
                </div>

                <div class="masthead__right grid--flex">
                    @if(changeHeader())
                        <div class="masthead__progress grid--flex flex--align-center">
                            <a class="js-header-progress" href="javascript:;">Course Progress</a>
                        </div>
                    @endif

                    <div class="masthead__calendar grid--flex">
                        <a class="grid--flex flex--align-center{!! set_active_link('calendar') !!}"" href="{{ route('calendar') }}"><i class="icon--calendar"></i></a>
                    </div>

                    @role('Administrator')
                        <div class="masthead__notifications grid--flex flex--align-center">
                            <a class="js-header-notifications" href="javascript:;">
                                <i class="icon--notification"></i>

                                @if(!empty($notifications['data']) && $notifications['count_unread'] > 0)
                                    <span class="masthead__notifications__count">{{ $notifications['count_unread'] }}</span>
                                @endif
                            </a>

                            <div class="masthead__notifications-outer-wrap">
                                <div class="masthead__notifications-wrap">
                                    <div class="masthead__notifications__header">
                                        <p>Notifications</p>
                                        <a href="#" class="js-notifications-mark-as-read" data-route="{{ route('notifications.markAsRead') }}">Mark all as read</a>
                                    </div>
                                    <ul class="masthead__notifications-list list--unstyled">
                                        @if(!empty($notifications['data']))
                                            @foreach($notifications['data']->take(5) as $notification)
                                                @include('lms.notifications.type.' . snake_case(class_basename($notification->type)))
                                            @endforeach
                                        @else
                                            <li class="masthead__notifications-list__item masthead__notifications-list__item--read-all">
                                                <a href="{{ route('notifications') }}"><strong>You are up-to-date</strong></a>
                                            </li>
                                        @endif

                                        <li class="masthead__notifications-list__item masthead__notifications-list__item--read-all">
                                            <a href="{{ route('notifications') }}"><strong>See all notifications.</strong></a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    @endrole

                    <div class="masthead__user grid--flex">
                        <ul class="list--inline grid--flex flex--align-center">
                            <!-- Authentication Links -->
                            @if (Auth::guest())
                                <li class="list__item"><a href="{{ route('login') }}">Login</a></li>
                                <li class="list__item"><a href="{{ route('register') }}">Register</a></li>
                            @else
                                <li class="list__item dropdown">
                                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                                        <i class="masthead__user-avatar">{{ substr(Auth::user()->name,0,1) }}</i><span class="masthead__user-name">{{ Auth::user()->name }}</span>
                                    </a>

                                    <div class="dropdown-menu-wrap">
                                        <ul class="dropdown-menu list--unstyled" role="menu">
                                            <li class="list__item"><a href="{{ route('user.profile') }}">My Profile</a></li>
                                            {{-- <li class="list__item"><a href="#">Progress</a></li> --}}
                                        <li class="list__item"><a href="{{ route('user.settings') }}">Settings</a></li>
                                        @role('Administrator')
                                            <li class="list__item"><a href="{{ url('admin') }}">Admin</a></li>
                                        @endrole
                                        <li class="list__item">
                                            <a href="{{ route('logout') }}"
                                                onclick="event.preventDefault();
                                                         document.getElementById('logout-form').submit();">
                                                Log Out
                                            </a>

                                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                                {{ csrf_field() }}
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            </li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>
    </div>
</header>

@if(changeHeader())
    <div class="course-progress-box">
        <div class="grid grid--w950">
            <ul class="grid--flex list--inline course-progress-box__list">
                <li class="course-progress-box__item course-progress-box__item--start"></li>

                <?php
                $all_watched = false;

                if ( isset($progress_items) ) { 
                    $current_lesson_id = null;
                    

                    if ( $progress_items->getNextSession() ) {
                        if ( isset($progress_items->getNextSession()->lesson) ) {
                            $current_lesson_id = $progress_items->getNextSession()->lesson->id;
                        } else {
                            $all_watched = true;
                        }
                    }

                    $module_count = 1;
                    $lesson_count = 1;

                    foreach ( $progress_items->modules as $module ) {
                        echo "<li class='course-progress-box__item grid--flex flex--column'>";

                        echo "<div class='course-progress-box__item--lessons grid--flex flex--space-around'>";
                        foreach ( $module->lessons as $lesson ) {
                            $current_lesson = null;
                            $current_lesson_class = null;
                            $is_completed = null;

                            if ( $lesson->is_completed ) {
                                $is_completed = " course-progress-box__item--lesson-mark__completed";
                            }

                            if ( isset($current_lesson_id) && $current_lesson_id ) {
                                if ( $current_lesson_id == $lesson->id ) {
                                    $current_lesson = "<div class='course-progress-box__item--lesson-current'>You are Here</div>";
                                    $current_lesson_class = " course-progress-box__item--lesson-mark__current ";
                                }
                            }

                            $lesson_box = "<div class='course-progress-box__item--lesson-box'>";
                            $lesson_box .= $current_lesson;                     
                            $lesson_box .= "<div class='course-progress-box__item--lesson-mark$current_lesson_class$is_completed'><div class='course-progress-box__item--lesson-mark__hover'></div></div>";
                            $lesson_box .= "<div class='course-progress-box__item--lesson-mark__info'>";
                            $lesson_box .= "<h6>$module->title</h6>";
                            $lesson_box .= "<h2>$lesson->title</h2>";
                            $lesson_box .= "<p>" . truncate_string($lesson->description) . "</p>";
                            // Check if module is locked
                            if ( $lesson->is_locked ) {
                                if ( $lesson->is_date_locked ) {
                                    $lesson_box .= "<div class='course-progress-box__item--lesson-mark__locked'>Locked</div>";
                                } else {
                                    $lesson_box .= "<div class='course-progress-box__item--lesson-mark__locked'>Locked</div>";
                                }
                            } else {
                                $lesson_box .= "<a href='". route('single.lesson', $lesson->slug) ."' class='course-progress-box__item--lesson-mark__info--link'>View This Lesson</a>";
                            }

                            $lesson_box .= "</div>";
                            $lesson_box .= "</div>";

                            echo $lesson_box;

                            $lesson_count++;
                        }
                        echo "</div>";

                        $module_box = "<div class='course-progress-box__item--module'>";    
                        $module_box .= "<h6>Module $module_count</h6>";
                        $module_box .= "<h2>$module->title</h2>";
                        $module_box .= "<p>" . truncate_string($module->description, 16) . "</p>";
                        // Check if module is locked
                        if ( $module->is_locked ) {
                            if ( $module->is_date_locked ) {
                                $module_box .= "<div class='course-progress-box__item--module__locked'>Locked</div>";
                            } else {
                                $module_box .= "<div class='course-progress-box__item--module__locked'>Locked</div>";
                            }
                        } else {
                            $module_box .= "<a href='" . route('single.module', $module->slug) . "' class='course-progress-box__item--module__link'>View This Module</a>";
                        }

                        $module_box .= "</div>";

                        echo $module_box;
                        
                        $module_count++;

                        echo "</li>";
                    }
                }
            ?>  
                <li class="course-progress-box__item course-progress-box__item--end @if($all_watched) course-progress-box__item course-progress-box__item--end__completed @endif"></li>
            </ul>
        </div>

        <button class="js-header-close-progress">Hide This</button>
    </div>
@endif

@if(!empty($askAlert))
    @include('lms.alerts.alert')
@endif

@if(!empty($lessonCongratulation))
    <div class="lesson-congratulation-box">
        <p><strong>Congratulation!</strong> Lorem ipsum dolor sit amet, consectetur adipisicing elit. Eaque sed, laudantium deleniti rem quasi? Dolores, aperiam consequatur praesentium a ut magni minima quae minus amet ea obcaecati dignissimos, alias cumque.</p>
        {{--<a target="_blank" href="{{ route('single.lesson.assessment.results', $lesson->slug) }}" data-user="{{ \Auth::user()->id }}" data-url="{{ route('single.lesson.assessment.check') }}" data-test="{{ $lesson->q_answered->assessment_id }}" data-popup="{{ route('lesson.testPopup', $lesson->id) }}" class="session-single__content-learn__default-btn-link js-retake-assessment">Take the assessment to see how much you've learned</a>--}}
    </div>
@endif