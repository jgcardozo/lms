@extends('layouts.app')

@section('title', 'User Settings')

@section('content')
    <main>
        <div class="grid grid--w950 spacer__top--big">
            <h1 class="page--title">My Profile</h1>

            <div class="user-settings">
                <div class="user-settings__main-links grid--flex">
                    <ul class="list--inline grid--flex">
                        <li class="grid--flex"><a class="grid--flex flex--align-center" href="{{ route('user.profile') }}">Account</a></li>
                        <li class="grid--flex"><a class="grid--flex flex--align-center" href="#">Progress</a></li>
                        <li class="grid--flex"><a class="grid--flex flex--align-center active" href="#">Settings</a></li>
                        <li class="grid--flex"><a class="grid--flex flex--align-center" href="{{ route('user.billing') }}">Billing</a></li>
                    </ul>
                </div>

                <div class="user-settings__content">            
                    <div class="grid--flex flex--space-between">
                        <div class="user-settings__info">
                            <h2>Change Password</h2>
                            <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. </p>
                        </div>

                        <div class="user-settings__manage">
                            <form class="block" method="POST" action="{{ route('user.settings') }}">
                                <div class="form-control grid--flex flex--space-between flex--align-center">
                                    <label for="oldpassword">Old password</label>
                                    <input type="password" id="oldpassword" name="oldpassword" />
                                </div>

                                <div class="form-control grid--flex flex--space-between flex--align-center">
                                    <label for="password">New password</label>
                                    <input type="password" id="password" name="password" />
                                </div>

                                <div class="form-control grid--flex flex--space-between flex--align-center">
                                    <label for="password_confirmation">Repeat password</label>
                                    <input type="password" id="password_confirmation" name="password_confirmation" />
                                </div>

                                {{ csrf_field() }}

                                <div class="form-control--submit grid--flex flex--end">
                                    <input type="submit" value="Save New Password" />
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>            
        </div>
    </main>

@endsection