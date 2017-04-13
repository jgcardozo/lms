@extends('layouts.app')

@section('title', 'Calendar')

@section('content')
	<main class="grid grid--w950 spacer__top--big">
		<h1 class="page--title">Calendar</h1>

		<section class="grid--flex flex--column">
			<div class="calendar">
				<div id="datepicker"></div>
			</div>
			<div class="events">
				<div class="events__count">
					<p>Showing <strong>{{ count($events) }} {{ count($events) > 1 ? 'Activities' : 'Activity'}}</strong></p>
				</div>

				<div class="events__item-featured grid--flex flex--space-between">
					<div class="events__item-featured--overlay"></div>

					<div class="events__item-featured-box grid--flex flex--column">
						<div class="events__item-featured-box--top grid--flex flex--align-center">
							<img class="events__item-featured-logo" src="{{ asset('images/icons/logo-big.svg') }}" alt="Course Event Name">
							<div class="events__item-featured-title">
                                <h2>{!! bold_first_word($events->first()->course->title) !!}</h2>
							</div>
						</div>

						<div class="events__item-featured-box--bottom">
							<h2>{{ $events->first()->title }}</h2>
							<p>{!! $events->first()->description !!}</p>
						</div>
						
					</div>

					<div class="events__item-featured-box grid--flex flex--column">
						<div class="events__item-featured-box--top">
							<h3>{{ \Carbon\Carbon::parse($events->first()->start_date)->format('F j') }}</h3>
							<h5>1pm/2pm ET</h5>
						</div>

                        @if(!empty($events->first()->url))
                            <div class="events__item-featured-box--bottom">
                                <a class="events__item-featured-link" href="{{ $events->first()->url }}" target="_blank">Apply Now</a>
                            </div>
                        @else
                            <div class="events__item-featured-box--bottom">
                                <a class="events__item-featured-link js-open-event" href="{{ route('event.show', $events->first()->id) }}" target="_blank">View</a>
                            </div>
                        @endif
					</div>
				</div>

                @foreach($events as $event)
                    @if(!$loop->first)
                        <div class="events__item grid--flex flex--space-between">
                            <div class="events__item--activity grid--flex flex--align-center flex--just-center">
                                <div class="events__item--activity-active"></div>
                            </div>

                            <div class="events__item--date">
                                <h3>{{ \Carbon\Carbon::parse($event->start_date)->format('F j') }}</h3>
                                <h5>1pm/2pm ET</h5>
                            </div>

                            <div class="events__item--content grid--flex">
                                <div class="events__item--content-image grid--flex flex--align-center flex--just-center">
                                    <div class="events__item--content-image--overlay"></div>

                                    <div class="events__item--content-logo">
                                        <img class="events__item--content-logo--icon" src="{{ asset('images/icons/logo-big.svg') }}" alt="Course Event Name">
                                    </div>
                                </div>

                                <div class="events__item--content-info grid--flex flex--column">
                                    <h5>{{ $event->course->title }}</h5>
                                    <h2>{{ $event->title }}</h2>
                                    <p>{!! $event->description !!}</p>
                                </div>
                            </div>

                            @if(!empty($event->url))
                                <div class="events__item--link grid--flex flex--align-center flex--end">
                                    <a class="events__item--link-apply" href="{{ $event->url }}" target="_blank">Apply Now</a>
                                </div>
                            @else
                                <div class="events__item--link grid--flex flex--align-center flex--end">
                                    <a class="events__item--link-view js-open-event" href="{{ route('event.show', $event->id) }}">View</a>
                                </div>
                            @endif
                        </div>
                    @endif
                @endforeach
			</div>
		</section>
	</main>

    <div class="event-single">
        <div class="event-single__content">
            <div class="event-single__close"></div>

            <div class="event-single__content-ajax">

            </div>

        </div>
    </div>
@endsection