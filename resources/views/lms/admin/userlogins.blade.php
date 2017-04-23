@extends('backpack::layout')

@section('header')
    <section class="content-header">
        <h1>
            User logins
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
                                        <th tabindex="0" rowspan="1" colspan="1">User</th>
                                        <th tabindex="1" rowspan="1" colspan="1">Number of IP addresses used</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @foreach($logins as $login)
                                        <tr role="row" class="odd">
                                            <td>
                                                {{ \App\Models\User::find($login->user_id) ? \App\Models\User::find($login->user_id)->first()->email : '' }}
                                            </td>
                                            <td>{{ $login->count }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>

                                <tfoot>
                                    <tr role="row">
                                        <th tabindex="0" rowspan="1" colspan="1">User</th>
                                        <th tabindex="1" rowspan="1" colspan="1">Number of IP addresses used</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection