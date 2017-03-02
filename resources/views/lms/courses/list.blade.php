@extends('lms.master')

@section('title', 'Courses')

@section('content')
    @foreach($courses as $course)
        <div class="block">
            <h2 class="block__title">{{ $course->title }}</h2>

            {!! $course->description !!}

            <a href="{{ route('single.course', $course->slug)  }}" class="block__link">Resume course</a>
        </div>
    @endforeach
@endsection