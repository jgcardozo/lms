@extends('backpack::layout')

@section('header')
    <section class="content-header">
        <h1>
            Logs
        </h1>
    </section>
@endsection

@section('content')
    <div class="row">
        <!-- <div class="col-md-12">
            <div class="box box-default">
                <div class="box-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <form method="get" action="{{ route('log.index') }}" class="form-inline">
                                {{ csrf_field() }}
                                @if($userFlag) <input type="hidden" name="user_id" value="{{ old('user_id') }}"> @endif
                                <div class="form-group @if($userFlag) hidden @endif">
                                    <label for="causer">Caused By</label>
                                    <select class="form-control" name="causer" id="causer">
                                        <option value="all" {{(old('causer') == 'all'?'selected':'')}}>All</option>
                                        <option value="user" {{(old('causer') == 'user'?'selected':'')}}>User</option>
                                        <option value="admin" {{(old('causer') == 'admin'?'selected':'')}}>Admin</option>
                                    </select>
                                </div>
                                <div class="form-group @if($userFlag) hidden @endif">
                                    <label for="cohort">Cohort</label>
                                    <select class="form-control" name="cohort" id="cohort">
                                        <option value="all">All</option>
                                        @foreach($cohorts as $cohort)
                                            <option value="{{ $cohort->id }}" {{(old('cohort') == $cohort->id?'selected':'')}}>{!! $cohort->name !!}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="action">Action</label>
                                    <select class="form-control" name="action" id="action">
                                        <option value="all">All</option>
                                        @foreach($actions as $action)
                                            <option value="{{ $action->id }}" {{(old('action') == $action->id?'selected':'')}}>{{ $action->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="activity">Activity</label>
                                    <select class="form-control" name="activity" id="activity">
                                        <option value="all">All</option>
                                        @foreach($activities as $activity)
                                            <option value="{{ $activity->id }}" {{(old('activity') == $activity->id?'selected':'')}}>{{ $activity->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <div class="input-group date dtp">
                                        <span class="input-group-addon" id="fromDate"><b>From Date</b></span>
                                        <input type="text" class="form-control" name="fromDate" id="fromDate" aria-describedby="basic-addon3" value="{{ old('fromDate') }}"  style="background-color: white">
                                        <span class="input-group-addon">
                                                <span class="glyphicon glyphicon-calendar"></span>
                                            </span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="input-group date dtp">
                                        <span class="input-group-addon" id="toDate"><b>To Date</b></span>
                                        <input type="text" class="form-control" name="toDate" id="toDate" aria-describedby="basic-addon3" value="{{ old('toDate') }}"  style="background-color: white">
                                        <span class="input-group-addon">
                                                <span class="glyphicon glyphicon-calendar"></span>
                                            </span>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-primary">Search</button>
                            </form>
                        </div>
                    </div>
                    <br>
                </div>
            </div>
        </div> -->

        <div id="app">
            <logs-table
                :cohorts={{ $cohorts }}
                :actions={{ $actions }}
                :activities={{ $activities }}
            ></logs-table>
        </div>
    </div>


@endsection

@section('after_styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.13/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.2.4/css/buttons.dataTables.min.css">
@endsection

@section('after_scripts')

@endsection
