<div class="mobile-menu">
	<div class="mobile-menu__content">
		<div class="mobile-menu__close"></div>		
		<ul class="mobile-menu__list list--inline grid--flex">
            <!-- Authentication Links -->
            @if (Auth::guest())
                <li class="list__item"><a href="{{ route('login') }}">Login</a></li>
                <li class="list__item"><a href="{{ route('register') }}">Register</a></li>
            @else
                <li class="list__item">
                    <a href="#">
                        <i class="masthead__user-avatar">{{ substr(Auth::user()->name,0,1) }}</i><span class="masthead__user-name">{{ Auth::user()->name }}</span>
                    </a>
                </li>

				@if(changeHeader())
					<li class="grid--flex"><a href="{{ route('single.course.starter', $progress_items->slug) }}" class="grid--flex flex--align-center {{ survey_check($progress_items) ? 'js-open-surveyPopup' : '' }}">Welcome</a></li>

					@if($progress_items->featured_coachingcall)
						<li class="list__item"><a href="{{ route('single.course.coaching-call', $progress_items->slug) }}">Q&A Calls</a></li>
					@endif

					@if($progress_items->facebook_group_id)
						<li class="list__item"><a href="{{ $progress_items->facebook_group_id }}">Facebook Group</a></li>
					@endif
					<li class="list__item"><a href="https://learn.askmethod.com/kickask-challenge/" target="_blank" class="">#Kick<strong>ASK</strong>&nbsp;Challenge</a></li>
				@endif
	            <li class="list__item"><a href="{{ route('user.profile') }}">My Profile</a></li>
	            {{-- <li class="list__item"><a href="#">Progress</a></li> --}}
	            <li class="list__item"><a href="{{ route('user.settings') }}">Settings</a></li>
	            <li class="list__item"><a href="{{ route('calendar') }}">Calendar</a></li>
	            @role('Administrator')
	                <li class="list__item"><a href="{{ url('admin') }}">Admin</a></li>
	            @endrole
	            <li class="list__item mobile-menu__logout">
	                <a href="{{ route('logout') }}"
	                    onclick="event.preventDefault();
	                             document.getElementById('logout-form').submit();">
	                    Log Out
	                </a>

	                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
	                    {{ csrf_field() }}
	                </form>
	            </li>                        
            @endif
        </ul>
	</div>
</div>