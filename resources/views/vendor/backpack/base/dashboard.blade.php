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
            <div class="box box-default" id="blurDiv" style="filter:blur(3px);">
                <div class="box-header with-border">
                    <div class="box-title">Statistics</div>
                    <button id="btnSync" class="btn btn-success" style="float: right" @if(\Illuminate\Support\Facades\Cache::has('updating')) {{ \Illuminate\Support\Facades\Cache::get('updating') }} @endif >
                        @if(\Illuminate\Support\Facades\Cache::has('last_sync'))
                            Synchronize ( {{ \Illuminate\Support\Carbon::createFromTimeString(\Illuminate\Support\Facades\Cache::get('last_sync'))->diffForHumans()  }} )
                        @else
                            Synchronize
                        @endif
                    </button>
                </div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-md-12">
                            <form action="#" method="get" class="form-inline">

                                <div class="form-group">
                                    <label for="course_id">Course:</label>
                                    <select class="form-control" name="course_id" id="course_id">

                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="cohort_id">Cohort:</label>
                                    <select class="form-control" name="cohort_id" id="cohort_id">
                                        <option value="" disabled selected>Select a cohort</option>

                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="module_id">Module:</label>
                                    <select class="form-control" name="module_id" id="module_id">
                                        <option value="" disabled selected>Select a module</option>

                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="lesson_id">Lesson:</label>
                                    <select class="form-control" name="lesson_id" id="lesson_id">
                                        <option value="" disabled selected>Select a lesson</option>

                                    </select>
                                </div>

                            </form>
                        </div>
                    </div>
                    <hr>
                    <div class="row" id="charts_div">
                        <div class="col-md-4">
                            <canvas id="modules"></canvas>
                            <div id="moduleLegend" class="text-left">

                            </div>
                        </div>
                        <div class="col-md-4">
                            <canvas id="lessons"></canvas>
                            <div id="lessonLegend" class="text-left">

                            </div>
                        </div>
                        <div class="col-md-4">
                            <canvas id="sessions"></canvas>
                            <div id="sessionLegend" class="text-left">

                            </div>
                        </div>
                        <h3 class="text-center" id="error_chart" style="display: none"> No results were found </h3>
                    </div>
                </div>
            </div>
            <div id="overlay_loading" style="position: absolute; top: 0; right: 0; bottom: 0; left: 0; background: url('{{asset('images/icons/loading-svg.svg')}}') center no-repeat;">
            </div>
        </div>
    </div>
@endsection

@section('custom_scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.js"></script>
    <script src="{{ asset('js/dashboard.js') }}"></script>
@endsection

@if (session('success_login'))
    <script>
        dataLayer.push({
            'event': 'login',
            'action': 'success'
        });
    </script>
@endif