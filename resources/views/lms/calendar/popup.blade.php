<div class="event-popup">
	<div class="event-popup__featured" @if($event->event_image) style="background-image: url({!! $event->getEventImageUrlAttribute() !!});" @endif>
		<div class="event-popup__featured-overlay"></div>

		<div class="event-popup__featured-content">
			<h2>{{ $event->title }}</h2>
			<p>{{ $event->short_description }}</p>
		</div>		
	</div>

	<div class="event-popup__content">
		<div class="event-popup__content-top grid--flex">
			<h3>{{ $event->getDate('start_date')->format('F j') }}</h3>
			<h5>{{ $event->getDate('start_date')->format('g:ia T') }}</h5>
		</div>

		<div class="event-popup__description">{!! $event->description !!}</div>

		@if(!empty($event->url))
			<a class="event-popup__apply-link js-event-apply" href="{{ $event->url }}" target="_blank">Register Now</a>
		@endif
	</div>
</div>