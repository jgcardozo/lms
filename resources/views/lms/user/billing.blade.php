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
                        {{--<li class="grid--flex"><a class="grid--flex flex--align-center" href="#">Progress</a></li>--}}
                        <li class="grid--flex"><a class="grid--flex flex--align-center" href="{{ route('user.settings') }}">Settings</a></li>
                        <li class="grid--flex"><a class="grid--flex flex--align-center active" href="#">Billing</a></li>
                    </ul>
                </div>

                <div class="user-settings__content">
                    <div class="user-settings__inner grid--flex flex--space-between">
                        <div class="user-settings__info">
                            <h2>Payment</h2>
                            <p>You may use this section to change your payment details.</p>
                        </div>

                        <div class="user-settings__manage">
                            <div class="ask-alert" style="display: none"></div>

                            @role('Administrator')
                            @foreach($courses as $course)
                                @if(!empty($course->billing_plans))
                                    <div id="course-{{ $course->id }}" class="billing-course" data-invoice="{{ @$course->billing_invoice_id }}">
                                        <a href="#" class="billing-course__details-btn js-open-billing-details">View billing details</a>
                                        <div class="billing-course__course-title">{!! $course->title !!}</div>

                                        <div class="billing-course__details">
                                            @if(!empty($course->billing_plans))
                                                <table>
                                                    <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>Due date</th>
                                                        <th>Amount</th>
                                                        <th>Status</th>
                                                    </tr>
                                                    </thead>

                                                    <tbody>
                                                    @foreach($course->billing_plans as $k => $plan)
                                                        <tr>
                                                            <td>{{ $k + 1 }}</td>
                                                            <td>{!! $plan['DateDue']->format('m-d-Y') !!}</td>
                                                            <td>{{ $plan['AmtDue'] }}</td>
                                                            <td>{!! $plan['Status'] == 2 ? '<span style="color: green;">Paid</span>' : '<span style="color: red;">Unpaid</span>' !!}</td>
                                                        </tr>
                                                    @endforeach
                                                    </tbody>
                                                </table>
                                            @endif

                                            <div class="billing-course__ccard" {{ !empty($course->billing_ccard['Id']) ? 'data-cardid=' . $course->billing_ccard['Id'] : '' }}>
                                                <form class="billing-course__ccard__form" method="POST" action="{{ route('user.billing.changecard', @$course->billing_invoice_id) }}">
                                                    <div class="form-control form-control__noteditable grid--flex flex--space-between flex--align-center">
                                                        <label for="nameoncard">Name On Card</label>
                                                        <input type="text" name="nameoncard" value="{{ @$course->billing_ccard['BillName'] }}" />
                                                    </div>

                                                    <div class="form-control form-control__noteditable grid--flex flex--space-between flex--align-center">
                                                        <label for="cc_number">Card Number</label>
                                                        <input type="text" class="js-stripe-cc-num" name="cc_number" value="{{ !empty($course->billing_ccard['Last4']) ? '**** **** **** ' . $course->billing_ccard['Last4'] : '' }}" />
                                                    </div>

                                                    <div class="form-control form-control__noteditable grid--flex flex--space-between flex--align-center">
                                                        <label for="cc_expiration">Expiration date</label>
                                                        <input type="text" class="js-stripe-cc-expiration" name="cc_expiration" value="{{ @$course->billing_ccard['ExpirationMonth'] }}/{{ @$course->billing_ccard['ExpirationYear'] }}" />
                                                    </div>

                                                    <div class="form-control form-control__noteditable grid--flex flex--space-between flex--align-center">
                                                        <label for="billing_address">Billing Address</label>
                                                        <input type="text" name="billing_address" value="{{ @$course->billing_ccard['BillAddress1'] }}" />
                                                    </div>

                                                    <div class="form-control--submit grid--flex flex--end">
                                                        <input type="submit" class="edit" value="Edit Billing Details" />
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach

                            @else
                                <h2>Coming soon</h2>
                            @endrole
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection