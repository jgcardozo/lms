@extends('lms.user.master')

@section('user.settings.content')
    <form class="block" method="POST" action="{{ route('user.settings') }}">
        <div class="form-control">
            <label for="oldpassword">Old password</label>
            <input type="password" id="oldpassword" name="oldpassword" />
        </div>

        <div class="form-control">
            <label for="password">New password</label>
            <input type="password" id="password" name="password" />
        </div>

        <div class="form-control">
            <label for="password_confirmation">Repeat password</label>
            <input type="password" id="password_confirmation" name="password_confirmation" />
        </div>

        {{ csrf_field() }}
        <input type="submit" value="Save" />
    </form>
@endsection