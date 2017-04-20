@extends('layouts.login')

@section('content')
    <main>
        <div class="login__logo"></div>
        
        <div class="grid grid--w950">
            <div class="login__component grid--flex">
                <div class="login__component-left">
                    <h2 class="login__title">Reset Password</h2>
                    
                    @if (session('status'))
                        <div class="alert alert-success">
                            {{ session('status') }}
                        </div>
                    @endif

                    <form class="login__form" role="form" method="POST" action="{{ route('password.email') }}">
                        {{ csrf_field() }}

                        <div class="login__form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                            <div class="login__form-box">
                                <input id="email" type="email" name="email" value="{{ old('email') }}" placeholder="Email Address" required>

                                @if ($errors->has('email'))
                                    <span class="login__help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="login__form-group">
                            <button type="submit" class="login__form-submit">
                                Send Reset Link
                            </button>
                        </div>
                    </form>
                </div>

                <div class="login__component-right">
                    <h3>We're Here To Help</h3>
                    <p>You can reset your password at any time. Simply enter the email address that you used to register and we'll send you an email with your username and a link to reset your password so you can keep #kickingask!</p>
                </div>
            </div>        
        </div>
    </main>
@endsection
