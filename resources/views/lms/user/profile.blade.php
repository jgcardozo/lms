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
                            <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. </p>
                        </div>

                        <div class="user-settings__manage">
                            <form class="block" method="POST" action="{{ route('user.profile') }}">
                                <div class="form-control grid--flex flex--space-between flex--align-center">
                                    <label for="name">Name</label>
                                    <input type="text" id="name" name="name" value="{{ $user->name }}" />
                                </div>

                                <div class="form-control grid--flex flex--space-between flex--align-center">
                                    <label for="email">Email</label>
                                    <input type="text" id="email" name="email" value="{{ $user->email }}" />
                                </div>

                                <div class="form-control grid--flex flex--space-between flex--align-center">
                                    <label for="phone1">Phone 1</label>
                                    <input type="text" id="phone1" name="phone1" value="{{ @$user->profile->phone1 }}" />
                                </div>

                                <div class="form-control grid--flex flex--space-between flex--align-center">
                                    <label for="phone2">Phone 2</label>
                                    <input type="text" id="phone2" name="phone2" value="{{ @$user->profile->phone2 }}" />
                                </div>

                                <div class="form-control grid--flex flex--space-between flex--align-center">
                                    <label for="company">Company</label>
                                    <input type="text" id="company" name="company" value="{{ @$user->profile->company }}" />
                                </div>

                                <div class="form-control grid--flex flex--space-between flex--align-center">
                                    <label for="address">Personal Address</label>
                                    <input type="text" id="address" name="address" value="{{ @$user->profile->address }}" />
                                </div>

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