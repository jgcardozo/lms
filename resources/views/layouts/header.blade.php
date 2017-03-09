<header class="masthead">
    <div class="grid grid--full">
        <div class="grid--flex flex--space-between flex--align-center">
            <div class="masthead__left grid--flex flex--align-center">
                <div class="masthead__logo">
                    <a class="masthead__logo-link" href="{{ url('/') }}">
                        {{ config('app.name', 'Laravel') }}
                    </a>
                </div>                 
            </div>

            <div class="masthead__right grid--flex flex--align-center">
                <ul class="list--inline">
                    <!-- Authentication Links -->
                    @if (Auth::guest())
                        <li class="list__item"><a href="{{ route('login') }}">Login</a></li>
                        <li class="list__item"><a href="{{ route('register') }}">Register</a></li>
                    @else
                        <li class="list__item dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                                {{ Auth::user()->name }} <span class="caret"></span>
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
</header>