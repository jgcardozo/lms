@extends('layouts.login')

@section('content')
    <main>
        <div class="login__logo"></div>

        <div class="grid grid--w950">
            <div class="login__component grid--flex">
                <div class="login__component-left">
                    <h2 class="login__title">Activate account</h2>

                    @if (session('status'))
                        <div class="alert alert-success">
                            {{ session('status') }}
                        </div>
                    @endif
                    
                    <form class="login__form" role="form" method="POST" action="{{ route('user.activate.do', $uuid) }}">
                        {{ csrf_field() }}

                        <div class="login__form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                            <div class="login__form-box">
                                <input id="password" type="password" class="form-control" name="password" placeholder="Password" required>

                                @if ($errors->has('password'))
                                    <span class="login__help-block">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="login__form-group{{ $errors->has('password_confirmation') ? ' has-error' : '' }}">
                            <div class="login__form-box">
                                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" placeholder="Confirm Password" required>

                                @if ($errors->has('password_confirmation'))
                                    <span class="login__help-block">
                                        <strong>{{ $errors->first('password_confirmation') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="login__form-group">
                            <button type="submit" class="login__form-submit">
                                Activate user
                            </button>
                        </div>
                    </form>
                </div>

                <div class="login__component-right">
                    <h3>We're Here To Help</h3>
                    <p>You can reset your password at any time. Simply enter the email address that you used to register and we'll send you an email with your username and a link to reset your password so you can keep #KickingASK!</p>
                </div>
            </div>        
        </div>
    </main>
@endsection
