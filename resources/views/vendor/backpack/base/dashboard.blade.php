@extends('backpack::layout')

@section('header')
    <section class="content-header">
      <h1>
        {{ trans('backpack::base.dashboard') }}<small>{{ trans('backpack::base.first_page_you_see') }}</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{ url(config('backpack.base.route_prefix', 'admin')) }}">{{ config('backpack.base.project_name') }}</a></li>
        <li class="active">{{ trans('backpack::base.dashboard') }}</li>
      </ol>
    </section>
@endsection


@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="box box-default">
                <div class="box-header with-border">
                    <div class="box-title">Statistics</div>
                </div>

                <div class="box-body">
                    @foreach($courses as $course)
                        <div class="course" style="margin-bottom: 20px; padding-bottom: 20px; border-bottom: 1px solid #DCDCDC">
                            <h4>{!! $course->title !!}</h4>
                            <div class="modules" style="padding-left: 20px">
                                @foreach($course->modules as $module)
                                    <h5>{!! $module->title !!}</h5>
                                    <div class="lessons" style="padding-left: 20px">
                                        @foreach($module->lessons as $lesson)
                                            <h5>
                                                {!! $lesson->title !!}
                                                <span style="font-weight: bold; display: inline-block; margin-left: 10px;">{{ $score[$lesson->id]['finished'] }} / {{ $score[$lesson->id]['total'] }} ({{ $score[$lesson->id]['percent'] }}%)</span>
                                            </h5>
                                        @endforeach
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endsection

@if (session('success_login'))
    <script>
        dataLayer.push({
            'event': 'login',
            'action': 'success'
        });
    </script>
@endif