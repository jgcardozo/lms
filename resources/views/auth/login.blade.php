@extends('layouts.login')

@section('content')    
    <main>
        <div class="login__logo"></div>

        <div class="grid grid--w950">
            <div class="login__component grid--flex">
                <div class="login__component-left">
                    <h2 class="login__title">Sign In</h2>

                    <form class="login__form" role="form" method="POST" action="{{ route('login') }}">
                        {{ csrf_field() }}

                        <div class="login__form-group{{ $errors->has('email') ? ' has-error' : '' }}">

                            <div class="login__form-box">
                                <input id="email" type="email" name="email" value="{{ old('email') }}" placeholder="Email Address" required autofocus>

                                @if ($errors->has('email'))
                                    <span class="login__help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="login__form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                            <div class="login__form-box">
                                <input id="password" type="password" name="password" placeholder="Password" required>

                                @if ($errors->has('password'))
                                    <span class="login__help-block">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="login__form-group grid--flex flex--space-between flex--align-center">
                            <button type="submit" class="login__form-submit">
                                Sign In
                            </button>

                            <a class="login__form-link" href="{{ route('password.request') }}">
                                Forgot Password?
                            </a>
                        </div>
                    </form>
                </div>

                <div class="login__component-right">
                    <h3>Welcome to the ASK Method!</h3>
                    <p>Grow Your Business</p>
                    <p>Make a Bigger Impact</p>
                    <p>Lead an Amazing Life!</p>
                </div>
            </div>        
        </div>
    </main>
@endsection
