@extends('backpack::layout')

@section('header')
    <section class="content-header">
        <h1>
            Analytics
        </h1>
    </section>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="box box-default">
                <div class="box-header with-border">
                    <div class="box-title">Filter</div>
                </div>

                <form method="get">
                    <div class="box-body">
                        <div class="form-group">
                            <label>Filter by cohort:</label>
                            <select name="cohort" class="form-control">
                                <option value=""> - </option>
                                @foreach($cohorts as $cohort)
                                    <option value="{{ $cohort->id }}">{!! $cohort->name !!}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="box-footer">
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="box box-default">
                <div class="box-header with-border">
                    <div class="box-title">User finished quiz - <strong>{{ count($results['data']) }} / {{ $results['total_users'] }}</strong></div>
                </div>

                <div class="box-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <table class="table table-bordered table-hover dataTable">
                                <thead>
                                    <tr role="row">
                                        <th tabindex="0" rowspan="1" colspan="1">E-mail</th>
                                        <th tabindex="1" rowspan="1" colspan="1">Name</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @foreach($results['data'] as $item)
                                        <tr role="row" class="odd">
                                            <td>{{ $item->email }}</td>
                                            <td>{{ $item->first_name }} {{ $item->last_name }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>

                                <tfoot>
                                    <tr role="row">
                                        <th tabindex="0" rowspan="1" colspan="1">E-mail</th>
                                        <th tabindex="1" rowspan="1" colspan="1">Name</th>
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