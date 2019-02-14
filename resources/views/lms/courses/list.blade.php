@extends('layouts.app')

@section('title', 'Courses')

@section('content')
	@if(! empty($courses))
		<main class="grid grid--lg spacer__top--big">
			<h1 class="page--title txt--capitalize pl-10">Welcome {{ auth()->user()->name }}</h1>

			<section class="grid--flex courseblock--flex">
			    @foreach($courses as $course)
					<div class="courseblock__wrapper">
					@if($course->is_locked)

			        	<div class="courseblock courseblock--locked" @if($course->featured_image) style="background-image: url({{ $course->featured_image_url }});" @endif>

                        <div class="courseblock--locked-sign"
                            @if($course->is_date_locked)
                                data-date=" until {{ date('d-m-Y', strtotime($lesson->getDate('lock_date'))) }}"
                            @endif
                        ><i class="icon--lock"></i></div>

                        <div class="courseblock__overlay courseblock__overlay--locked"></div>
			        @else
						<div class="courseblock" @if($course->featured_image) style="background-image: url({{ $course->featured_image_url }});" @endif>
			        	<div class="courseblock__overlay"></div>
			        @endif

			        	<div class="courseblock__content">
			        		<div class="courseblock__logo" @if($course->logo_image) style="background-image: url({{ $course->getLogoImageUrlAttribute() }});" @endif></div>

							<div class="courseblock__content__wrapper">
								<h2 class="courseblock__title ucase">{!! bold_first_word($course->title) !!}</h2>

								<p>{!! $course->short_description !!}</p>
								@if($course->is_locked)
									@if($course->apply_now)
										<a href="{{ $course->apply_now }}" class="courseblock__link" target="_blank">{!! $course->apply_now_label !!}</a>
									@else
										<button type="button" class="courseblock__link">Coming Soon</button>
									@endif
								@else
									@if($course->id == 12)
									<a href="{{ link_to_lms2() }}" class="courseblock__link">Access Training</a>
									@else
									<a href="{{ route('single.course', $course->slug) }}" class="courseblock__link">Access Training</a>
									@endif
								@endif
							</div>
			        	</div>
			        </div>
				</div>
			    @endforeach

				@if(!empty($bonuses))
					<div class="courseblock__wrapper">
						<div class="courseblock" @if($course->featured_image) style="background-image: url({{ $course->featured_image_url }});" @endif>
							<div class="courseblock__overlay"></div>

							<div class="courseblock__content">
								<div class="courseblock__logo"></div>
									<div class="courseblock__content__wrapper">
										<h2 class="courseblock__title ucase">Bonuses</h2>

										<p>Find all of your bonus trainings here</p>
									<a href="{{ route('bonus') }}" class="courseblock__link">Access bonuses</a>
								</div>
							</div>
						</div>
					</div>
				@endif
		    </section>
	    </main>
	@endif
@endsection