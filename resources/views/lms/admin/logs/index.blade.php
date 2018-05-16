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
                                    <tr>
                                        <td>{{ $log->id }}</td>
                                        <td>{{ $log->user->name }}</td>
                                        <td>{{ $log->action->name }}</td>
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