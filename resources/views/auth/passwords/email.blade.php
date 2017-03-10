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
                    <h3>Lorem ipsum dolor sit amet, consectetur adipiscing elit.</h3>
                    <p>Donec faucibus sagittis posuere. Maecenas consectetur vel eros elementum ultricies. Pellentesque turpis lorem, tincidunt accumsan magna vel, iaculis convallis sapien. Suspendisse vestibulum varius magna, nec venenatis est cursus nec.</p>
                </div>
            </div>        
        </div>
    </main>
@endsection
