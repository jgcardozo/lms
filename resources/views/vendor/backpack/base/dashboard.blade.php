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
                        </div>
                        <div class="col-md-4">
                            <canvas id="lessons"></canvas>
                        </div>
                        <div class="col-md-4">
                            <canvas id="sessions"></canvas>
                        </div>
                        <h3 class="text-center" id="error_chart" style="display: none" "> No results were found </h3>
                    </div>
                </div>
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