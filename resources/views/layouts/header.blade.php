<header class="masthead">
    <div class="grid grid--full">
        <div class="grid--flex flex--space-between flex--align-center">
            <div class="masthead__logo">
                 <a href="{{ url('/') }}">
                    {{ config('app.name', 'Laravel') }}
                </a>
            </div>

            <div class="masthead__user--info">
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

                            <ul class="dropdown-menu" role="menu">
                                <li>
                                    <a href="{{ route('logout') }}"
                                        onclick="event.preventDefault();
                                                 document.getElementById('logout-form').submit();">
                                        Logout
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