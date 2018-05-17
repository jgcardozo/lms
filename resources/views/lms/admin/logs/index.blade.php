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
        <div class="col-md-12">

            <div class="box box-default">

                <div class="box-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <form method="get" action="{{ route('log.index') }}" class="form-inline">
                                {{ csrf_field() }}
                                <div class="form-group">
                                    <label for="causer">Caused By</label>
                                    <select class="form-control" name="causer" id="causer">
                                        <option value="all" {{(old('causer') == 'all'?'selected':'')}}>All</option>
                                        <option value="user" {{(old('causer') == 'user'?'selected':'')}}>User</option>
                                        <option value="admin" {{(old('causer') == 'admin'?'selected':'')}}>Admin</option>
                                    </select>
                                </div>
                                <div class="form-group">
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
                                        <span class="input-group-addon" id="basic-addon3"><b>From Date</b></span>
                                        <input type="text" class="form-control" name="fromDate" id="fromDate" aria-describedby="basic-addon3" value="{{ old('fromDate') }}"  style="background-color: white">
                                        <span class="input-group-addon">
                                                <span class="glyphicon glyphicon-calendar"></span>
                                            </span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="input-group date dtp">
                                        <span class="input-group-addon" id="basic-addon3"><b>To Date</b></span>
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
                    <div class="row">
                        <div class="col-sm-12">
                            <table class="table table-bordered table-hover dataTable">
                                <thead>
                                <tr role="row">
                                    <th tabindex="0" rowspan="1" colspan="1">Log Id</th>
                                    <th tabindex="1" rowspan="1" colspan="1">User</th>
                                    <th tabindex="2" rowspan="1" colspan="1">Action</th>
                                    <th tabindex="3" rowspan="1" colspan="1">Subject</th>
                                    <th tabindex="4" rowspan="1" colspan="1">Timestamp</th>
                                </tr>
                                </thead>

                                <tbody>
                                @foreach($logs as $log)
                                    @if(!empty(old()))
                                        @if(old('cohort') !== 'all')
                                            @if(count($log->user->cohorts->where('id',old('cohort'))) == 0)
                                                @continue
                                            @endif
                                        @endif
                                    @endif
                                    <tr>
                                        <td>{{ $log->id }}</td>
                                        <td>{{ $log->user->name }}</td>
                                        <td>{{ $log->action->name }}</td>
                                        @if(empty($log->activity) && empty($log->subject))
                                            <td></td>
                                        @else
                                        @if(empty($log->activity))
                                            <td>{{ $log->subject->title }}</td>
                                        @else
                                            @if($log->activity->name === "Admin")
                                                @if(empty($log->subject->title))
                                                    @if(empty($log->subject->name))
                                                        <td>{{ $log->subject_type}} <sup>here</sup></td>
                                                    @else
                                                        <td>{{ $log->subject->name}}</td>
                                                    @endif
                                                @else
                                                    <td>{{ $log->subject->title }}</td>
                                                @endif
                                            @else
                                                <td>{{ $log->activity->name }}</td>
                                            @endif
                                        @endif
                                        @endif
                                        <td>{{ $log->created_at }}</td>
                                    </tr>
                                @endforeach
                                </tbody>

                                <tfoot>
                                <tr role="row">
                                    <th tabindex="0" rowspan="1" colspan="1">Log Id</th>
                                    <th tabindex="1" rowspan="1" colspan="1">User</th>
                                    <th tabindex="2" rowspan="1" colspan="1">Action</th>
                                    <th tabindex="3" rowspan="1" colspan="1">Subject</th>
                                    <th tabindex="4" rowspan="1" colspan="1">Timestamp</th>
                                </tr>
                                </tfoot>
                            </table>

                            {{$logs->links()}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection