@extends('layouts.app')

@section('title', 'Courses')

@section('content')
	@if(! empty($courses))
		<main class="grid grid--w950 spacer__top--big">
			<h1 class="page--title">Courses</h1>

			<section class="grid--flex flex--column">
			    @foreach($courses as $course)
			        <div class="courseblock">
			        	<div class="courseblock__overlay"></div>

			        	<div class="courseblock__content">
			        		<h2 class="courseblock__title">{{ $course->title }}</h2>

				            <p>{!! $course->short_description !!}</p>

				            <a href="{{ route('single.course', $course->slug)  }}" class="courseblock__link">Resume course</a>
			        	</div>			            
			        </div>
			    @endforeach
		    </section>
	    </main>
	@endif
@endsection