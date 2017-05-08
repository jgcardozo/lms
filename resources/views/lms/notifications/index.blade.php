@extends('layouts.app')

@section('title', 'Notifications')

@section('content')
    <main class="grid grid--w950 spacer__top--big">
        <h1 class="page--title">Notifications</h1>

        <section class="grid--flex flex--column">
            @if(!empty($user_notifications['general']))
                <div class="allnotifications">
                    @foreach($user_notifications['general'] as $notification)
                        @include('lms.notifications.type.' . snake_case(class_basename($notification->type)) . '-full')
                    @endforeach
                </div>
            @endif
        </section>
    </main>
@endsection