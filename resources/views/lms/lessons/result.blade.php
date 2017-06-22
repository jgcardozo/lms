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

                @if($score)
                    <div class="lesson-result__score__num">
                        <p>Your score:</p>
                        <h4>{{ number_format(($score / 10), 1, '.', ',') }}</h4>
                    </div>
                @endif
            </div>

            <br />

            <div class="lesson-result__main-video">
                <div class="wistia_responsive_padding">
                    <div class="wistia_responsive_wrapper">
                        <div class="wistia_embed wistia_async_{{ !empty($lesson->q_answered) ? $lesson->q_answered->video_url : '' }}"></div>
                    </div>
                </div>

                <h4>{!! !empty($lesson->q_answered) ? $lesson->q_answered->video_title : '' !!}</h4>
                {!! !empty($lesson->q_answered) ? $lesson->q_answered->description : '' !!}
            </div>

            <div class="lesson-result__videos">
                @foreach($lesson->questions as $question)
                    @if(!empty($lesson->q_answered) && ($question->id != $lesson->q_answered->id))
                        <div class="lesson-result__video">
                            <img src="{{ $question->featured_image_url }}" />

                            <h5>{{ $question->video_title }}</h5>
                            <p>{!! truncate_string($question->description, 10) !!}</p>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    </main>
@endsection