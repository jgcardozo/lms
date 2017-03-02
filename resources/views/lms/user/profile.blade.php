@extends('lms.user.master')

@section('user.settings.content')
    <form class="block" method="POST" action="{{ route('user.profile') }}">
        <div class="form-control">
            <label for="name">Name</label>
            <input type="text" id="name" name="name" value="{{ $user->name }}" />
        </div>

        <div class="form-control">
            <label for="email">Email</label>
            <input type="text" id="email" name="email" value="{{ $user->email }}" />
        </div>

        <div class="form-control">
            <label for="phone1">Phone 1</label>
            <input type="text" id="phone1" name="phone1" value="{{ @$user->profile->phone1 }}" />
        </div>

        <div class="form-control">
            <label for="phone2">Phone 2</label>
            <input type="text" id="phone2" name="phone2" value="{{ @$user->profile->phone2 }}" />
        </div>

        {{ csrf_field() }}
        <input type="submit" value="Save" />
    </form>
@endsection