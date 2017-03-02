@extends('lms.master')

@section('title', 'My profile')

@section('content')
    <div class="user-settings">
        <div class="user-settings__navigation">
            @include('lms.user.nav')
        </div>

        <div class="user-settings__content">
            @yield('user.settings.content')
        </div>
    </div>
@endsection