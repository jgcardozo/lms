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