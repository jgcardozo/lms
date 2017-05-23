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
                                        <th tabindex="0" rowspan="1" colspan="1">Log type</th>
                                        <th tabindex="1" rowspan="1" colspan="1">Log description</th>
                                        <th tabindex="2" rowspan="1" colspan="1">User</th>
                                        <th tabindex="3" rowspan="1" colspan="1">Subject</th>
                                        <th tabindex="4" rowspan="1" colspan="1">Properties</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @foreach($logs as $log)
                                        <tr role="row" class="odd">
                                            <td>
                                                @if(View::exists('lms.admin.logs.' . $log->log_name))
                                                    @include('lms.admin.logs.' . $log->log_name, ['log_type' => $log->log_name])
                                                @else
                                                    <span>
                                                        {{ $log->log_name }}
                                                    </span>
                                                @endif
                                            </td>
                                            <td>{!! $log->description !!}</td>
                                            <td>{{ @$log->causer->email }}</td>
                                            <td>{{ !empty($log->subject) ? $log->subject->title . ' [' . $log->subject->id . ']' : '' }}</td>
                                            <td>
                                                @foreach($log->properties as $key => $p)
                                                    {{ ucfirst($key) }}: <strong>{{ $p }}</strong><br/>
                                                @endforeach
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>

                                <tfoot>
                                    <tr role="row">
                                        <th tabindex="0" rowspan="1" colspan="1">Log type</th>
                                        <th tabindex="1" rowspan="1" colspan="1">Log description</th>
                                        <th tabindex="2" rowspan="1" colspan="1">User</th>
                                        <th tabindex="3" rowspan="1" colspan="1">Subject</th>
                                        <th tabindex="4" rowspan="1" colspan="1">Properties</th>
                                    </tr>
                                </tfoot>
                            </table>

                            {{ $logs->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection