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
                        <li class="grid--flex"><a class="grid--flex flex--align-center" href="{{ route('user.settings') }}">Settings</a></li>
                        <li class="grid--flex"><a class="grid--flex flex--align-center active" href="#">Billing</a></li>
                    </ul>
                </div>

                <div class="user-settings__content">
                    <div class="grid--flex flex--space-between">
                        <div class="user-settings__info">
                            <h2>Payment</h2>
                            <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. </p>
                        </div>

                        <div class="user-settings__manage">
                            <form class="block" method="POST" action="{{ route('user.billing') }}">
                                @foreach($courses as $course)
                                    <div id="course-{{ $course->id }}" class="billing-course" data-invoice="{{ $course->is_invoice_details }}">
                                        <a href="#" class="billing-course__details-btn js-open-billing-details">View billing details</a>
                                        <div class="billing-course__course-title">{{ $course->title }}</div>

                                        <div class="billing-course__details">
                                            {{ dump($course->is_invoice_details) }}
                                        </div>
                                    </div>
                                @endforeach
                                {{--
                                <div class="form-control grid--flex flex--space-between flex--align-center">
                                    <label for="nameoncard">Name On Card</label>
                                    <input type="text" id="nameoncard" name="nameoncard" />
                                </div>

                                <div class="form-control grid--flex flex--space-between flex--align-center">
                                    <label for="cc_number">Card Number</label>
                                    <input type="text" id="cc_number" name="cc_number" />
                                </div>

                                <div class="form-control grid--flex flex--space-between flex--align-center">
                                    <label for="cc_expiration">Expiration date</label>
                                    <input type="text" id="cc_expiration" name="cc_expiration" />
                                </div>

                                <div class="form-control grid--flex flex--space-between flex--align-center">
                                    <label for="billing_address">Billing Address</label>
                                    <input type="text" id="billing_address" name="billing_address" />
                                </div>
                                --}}

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