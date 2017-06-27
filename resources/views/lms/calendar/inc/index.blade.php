<div class="events">
    <div class="events__count">
        <p>Showing <strong>{{ count($futureEvents) }} {{ count($futureEvents) > 1 ? 'Activities' : 'Activity'}}</strong></p>
    </div>

    @if(!$futureEvents->isEmpty())
        <div class="events__item-featured grid--flex flex--space-between">
            @if($futureEvents->first()->is_locked)
                <div class="events__item--locked"></div>
            @endif
            <div class="events__item-featured--overlay"></div>

            <div class="events__item-featured-box grid--flex flex--column">
                <div class="events__item-featured-box--top grid--flex flex--align-center">
                    <img class="events__item-featured-logo" src="{{ asset('images/icons/logo-big.svg') }}" alt="Course Event Name">
                    <div class="events__item-featured-title">
                        <h2>{!! bold_first_word($futureEvents->first()->course->title) !!}</h2>
                    </div>
                </div>

                <div class="events__item-featured-box--bottom">
                    <h2>{{ $futureEvents->first()->title }}</h2>
                    <p>{!! $futureEvents->first()->short_description !!}</p>
                </div>

            </div>

            <div class="events__item-featured-box grid--flex flex--column">
                <div class="events__item-featured-box--top">
                    <h3>{{ $futureEvents->first()->getDate('start_date')->format('F j') }}</h3>
                    <h5>{{ $futureEvents->first()->getDate('start_date')->format('g:ia T') }}</h5>
                </div>

                <div class="events__item-featured-box--bottom">
                    <a class="events__item-featured-link {{ !$futureEvents->first()->is_locked ? 'js-open-event' : '' }}" href="{{ route('event.show', $futureEvents->first()->id) }}" target="_blank">View</a>
                </div>
            </div>
        </div>

        @foreach($futureEvents as $event)
            @if(!$loop->first)
                <div class="events__item grid--flex flex--space-between">
                    @if($event->is_locked)
                        <div class="events__item--locked"></div>
                    @endif
                    <div class="events__item--activity grid--flex flex--align-center flex--just-center">
                        <div class="events__item--activity-active"></div>
                    </div>

                    <div class="events__item--date">
                        <h3>{{ $event->getDate('start_date')->format('F j') }}</h3>
                        <h5>{{ $event->getDate('start_date')->format('g:ia T') }}</h5>
                    </div>

                    <div class="events__item--content grid--flex">
                        <div class="events__item--content-image grid--flex flex--align-center flex--just-center">
                            <div class="events__item--content-image--overlay"></div>

                            <div class="events__item--content-logo">
                                <img class="events__item--content-logo--icon" src="{{ asset('images/icons/logo-big.svg') }}" alt="Course Event Name">
                            </div>
                        </div>

                        <div class="events__item--content-info grid--flex flex--column">
                            <h5>{!! $event->course->title !!}</h5>
                            <h2>{{ $event->title }}</h2>
                            <p>{!! $event->short_description !!}</p>
                        </div>
                    </div>

                    <div class="events__item--link grid--flex flex--align-center flex--end">
                        <a class="events__item--link-view {{ !$event->is_locked ? 'js-open-event' : '' }}" href="{{ route('event.show', $event->id) }}">View</a>
                    </div>
                </div>
            @endif
        @endforeach
    @endif
</div>