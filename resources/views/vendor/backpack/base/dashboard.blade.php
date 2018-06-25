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
                        <div class="col-md-4">
                            <canvas id="modules"></canvas>
                        </div>
                        <div class="col-md-4">
                            <canvas id="lessons"></canvas>
                        </div>
                        <div class="col-md-4">
                            <canvas id="sessions"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('custom_scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.js"></script>
    <script>
        $(document).ready(function () {
            var ctx = $('#modules');
            var modulesChart = new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: [@foreach($modulePieChart as $key => $value) '{{ $key }}' ,@endforeach],
                    datasets: [{
                        label: '% Completed',
                        data: [@foreach($modulePieChart as $key => $value) {{ $value }} ,@endforeach]
                    }]
                },
                options: Chart.defaults.pie
            });

            @if(!empty($lessonPieChart))
            var ctx1 = $('#lessons');
            var lessonsChart = new Chart(ctx1, {
                type: 'pie',
                data: {
                    labels: [@foreach($lessonPieChart as $key => $value) '{{ $key }}' ,@endforeach],
                    datasets: [{
                        label: '% Completed',
                        data: [@foreach($lessonPieChart as $key => $value) {{ $value }} ,@endforeach]
                    }]
                },
                options: Chart.defaults.pie
            });
            @endif

            @if(!empty($sessionPieChart))
            var ctx2 = $('#sessions');
            var sessionsChart = new Chart(ctx2, {
                type: 'pie',
                data: {
                    labels: [@foreach($sessionPieChart as $key => $value) '{{ $key }}' ,@endforeach],
                    datasets: [{
                        label: '% Completed',
                        data: [@foreach($sessionPieChart as $key => $value) {{ $value }} ,@endforeach]
                    }]
                },
                options: Chart.defaults.pie
            });
            @endif
        })
    </script>
@endsection

@if (session('success_login'))
    <script>
        dataLayer.push({
            'event': 'login',
            'action': 'success'
        });
    </script>
@endif