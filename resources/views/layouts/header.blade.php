<header class="masthead">
    <div class="grid grid--full">
        <div class="grid--flex flex--space-between">
            <div class="masthead__left grid--flex">
                <div class="masthead__logo grid--flex flex--align-center">
                    <a class="masthead__logo-link" href="{{ url('/') }}">
                        {{ config('app.name', 'Laravel') }}
                    </a>
                </div>

                <div class="masthead__main-links grid--flex">
                    <ul class="list--inline grid--flex">
                        <li class="grid--flex"><a class="grid--flex flex--align-center" href="#">Training</a></li>
                        <li class="grid--flex"><a class="grid--flex flex--align-center" href="#">Coaching Calls</a></li>
                        <li class="grid--flex"><a class="grid--flex flex--align-center" href="#"><i class="icon--facebook"></i> Facebook Group</a></li>
                    </ul>
                </div>                
            </div>

            <div class="masthead__right grid--flex">
                <div class="masthead__progress grid--flex flex--align-center">
                    <a href="#">Course Progress</a>
                </div>
                
                <div class="masthead__calendar grid--flex flex--align-center">
                    <a href="#"><i class="icon--calendar"></i></a>
                </div>

                <div class="masthead__notifications grid--flex flex--align-center">
                    <a href="#"><i class="icon--notification"></i></a>
                </div>

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

                                <ul class="dropdown-menu list--unstyled" role="menu">
                                    <li class="list__item"><a href="#">My Profile</a></li>
                                    <li class="list__item"><a href="#">Progress</a></li>
                                    <li class="list__item"><a href="#">Settings</a></li>
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
                            </li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>
    </div>
</header>
