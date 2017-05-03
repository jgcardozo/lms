@extends('layouts.app')

@section('title', 'Courses')

@section('content')
	@if(! empty($courses))
		<main class="grid grid--w950 spacer__top--big">
			<h1 class="page--title">Courses</h1>

			<section class="grid--flex flex--column">
			    @foreach($courses as $course)
			    <?php //dd($course); ?>
			    	@if($course->is_locked)
			        	<div class="courseblock courseblock--locked" @if($course->featured_image) style="background-image: url({{ $course->featured_image_url }});" @endif>

                        <div class="courseblock--locked-sign"
                            @if($course->is_date_locked)
                                data-date=" until {{ date('d-m-Y', strtotime($course->lock_date)) }}"
                            @endif
                        ><i class="icon--lock"></i></div> 

                        <div class="courseblock__overlay courseblock__overlay--locked"></div>                               
			        @else
						<div class="courseblock" @if($course->featured_image) style="background-image: url({{ $course->featured_image_url }});" @endif>
			        	<div class="courseblock__overlay"></div>
			        @endif

			        	<div class="courseblock__content">
			        		<div class="courseblock__logo" @if($course->logo_image) style="background-image: url({{ $course->getLogoImageUrlAttribute() }});" @endif></div>
			        		
			        		<h2 class="courseblock__title ucase">{!! bold_first_word($course->title) !!}</h2>

				            <p>{!! $course->short_description !!}</p>
							
							@if($course->is_locked)
								@if($course->apply_now)
				            		<a href="{{ $course->apply_now }}" class="courseblock__link" target="_blank">{!! $course->apply_now_label !!}</a>
				            	@else
					            	<button type="button" class="courseblock__link">Coming Soon</button>
					            @endif
				            @else
				            	<a href="{{ route('single.course', $course->slug) }}" class="courseblock__link">Access Training</a>
				            @endif
			        	</div>			            
			        </div>
			    @endforeach
		    </section>
	    </main>
	@endif
@endsection