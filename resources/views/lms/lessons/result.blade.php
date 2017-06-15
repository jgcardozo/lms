@extends('layouts.app')

@section('title', $lesson->title)

@section('scripts_before')
    <script src="//fast.wistia.com/assets/external/E-v1.js" async></script>
@endsection

@section('content')
    <main>
        <div class="grid grid--w950 course-single__content" style="padding-bottom: 0">
            <div class="lesson-result__score" style="text-align: center">
                <img src="{{ URL::to('/') }}/images/student.png" />
            </div>

            <br />

            <div class="lesson-result__main-video">
                <div class="wistia_responsive_padding">
                    <div class="wistia_responsive_wrapper">
                        <div class="wistia_embed wistia_async_{{ $lesson->q_answered->video_url }}"></div>
                    </div>
                </div>

                <h4>{!! $lesson->q_answered->video_title !!}</h4>
                {!! $lesson->q_answered->description !!}
            </div>

            <div class="lesson-result__videos">
                @foreach($lesson->questions as $question)
                    @if($question->id != $lesson->q_answered->id)
                        <div class="lesson-result__video">
                            <img src="{{ $question->featured_image_url }}" />

                            <h5>{{ $question->video_title }}</h5>
                            {!! $question->description !!}
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    </main>
@endsection