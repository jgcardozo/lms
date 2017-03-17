@extends('layouts.app')

@section('title', 'Courses')

@section('content')
	@if(! empty($courses))
		<main class="grid grid--w950 spacer__top--big">
			<h1 class="page--title">Courses</h1>

			<section class="grid--flex flex--column">
			    @foreach($courses as $course)
			    	@if($course->is_locked)
			        	<div class="courseblock courseblock--locked">

                        <div class="courseblock--locked-sign"
                            @if($course->is_date_locked)
                                data-date=" until {{ date('d-m-Y', strtotime($course->lock_date)) }}"
                            @endif
                        ><i class="icon--lock"></i></div> 

                        <div class="courseblock__overlay courseblock__overlay--locked"></div>                               
			        @else
						<div class="courseblock">
			        	<div class="courseblock__overlay"></div>
			        @endif

			        	<div class="courseblock__content">
			        		<div class="courseblock__logo"></div>
			        		
			        		<h2 class="courseblock__title ucase">{!! bold_first_word($course->title) !!}</h2>

				            <p>{!! $course->short_description !!}</p>
							
							@if($course->is_locked)
				            	<a href="#" class="courseblock__link">Apply Now</a>
				            @else
				            	<a href="{{ route('single.course', $course->slug)  }}" class="courseblock__link">Resume</a>
				            @endif
			        	</div>			            
			        </div>
			    @endforeach
		    </section>
	    </main>
	@endif
@endsection