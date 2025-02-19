@extends('layouts.app')

@section('title', 'User Profile')

@section('content')
    <main>
        <div class="grid grid--w950 spacer__top--big">
            <h1 class="page--title">My Profile</h1>

            <div class="user-settings">
                <div class="user-settings__main-links grid--flex">
                    <ul class="list--inline grid--flex">
                        <li class="grid--flex"><a class="grid--flex flex--align-center active" href="javascript:;">Account</a></li>
                        {{--<li class="grid--flex"><a class="grid--flex flex--align-center" href="#">Progress</a></li>--}}
                        <li class="grid--flex"><a class="grid--flex flex--align-center" href="{{ route('user.settings') }}">Settings</a></li>
                        <li class="grid--flex"><a class="grid--flex flex--align-center" href="{{ route('user.billing') }}">Billing</a></li>
                    </ul>
                </div>

                <div class="user-settings__content">
                    <div class="user-settings__inner grid--flex flex--space-between">
                        <div class="user-settings__info">
                            <h2>Profile Details</h2>
                            <p>You may use this section to update your name, email address and contact details.</p>
                        </div>

                        <div class="user-settings__manage">
                            @if(Session::has('message'))
                                <div class="ask-alert ask-alert--success">{{ Session::get('message') }}</div>
                            @endif

                            @if(Session::has('errors'))
                                <div class="ask-alert ask-alert--critical">Please fill the required fields</div>
                            @endif

                            <form class="block" method="POST" action="{{ route('user.profile') }}">

                                <div class="form-control grid--flex flex--space-between flex--align-center {{ $errors->has('first_name') ? ' has-error' : '' }}">
                                    <label for="first_name">First Name<sup>*</sup></label>
                                    <input type="text" id="first_name" name="first_name" value="{{ old('first_name', @$user->profile->first_name) }}" />
                                </div>

                                <div class="form-control grid--flex flex--space-between flex--align-center {{ $errors->has('last_name') ? ' has-error' : '' }}">
                                    <label for="last_name">Last Name<sup>*</sup></label>
                                    <input type="text" id="last_name" name="last_name" value="{{ old('last_name', @$user->profile->last_name) }}" />
                                </div>

                                <div class="form-control grid--flex flex--space-between flex--align-center {{ $errors->has('email') ? ' has-error' : '' }}">
                                    <label for="email">Email<sup>*</sup></label>
                                    <input type="text" id="email" name="email" value="{{ old('email', $user->email) }}" />
                                </div>

                                <div class="form-control grid--flex flex--space-between flex--align-center {{ $errors->has('phone1') ? ' has-error' : '' }}">
                                    <label for="phone1">Phone<sup>*</sup></label>
                                    <input type="text" id="phone1" name="phone1" value="{{ old('phone1', @$user->profile->phone1) }}" />
                                </div>

                                <div class="form-control grid--flex flex--space-between flex--align-center {{ $errors->has('company') ? ' has-error' : '' }}">
                                    <label for="company">Company</label>
                                    <input type="text" id="company" name="company" value="{{ old('company', @$user->profile->company) }}" />
                                </div>

                                <div class="form-control grid--flex flex--space-between flex--align-center">
                                    <label for="company">Timezone</label>
                                    <select name="timezone">
                                        <option value="">Select your timezone</option>
                                        @foreach($timezones as $k => $timezone)
                                            <option value="{{ $k }}" {{ $user->timezone == $k ? 'selected' : '' }}>{{ $timezone }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <p style="text-align: right">*required</p>

                                {{ csrf_field() }}

                                <div class="form-control--submit grid--flex flex--end">
                                    <input type="submit" value="Save Changes" />
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

@endsection