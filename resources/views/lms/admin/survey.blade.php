@extends('backpack::layout')

@section('header')
    <section class="content-header">
        <h1>
            Survey
        </h1>
    </section>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="box box-default">

                <div class="box-header with-border">
                    <div class="box-title">Survey results</div>
                </div>

                <div class="box-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <table id="surveyTable" class="table table-bordered table-hover dataTable">
                                <thead>
                                    <tr role="row">
                                        <th tabindex="0" rowspan="1" colspan="1">Q #1</th>
                                        <th tabindex="1" rowspan="1" colspan="1">Q #2</th>
                                        <th tabindex="2" rowspan="1" colspan="1">Q #3</th>
                                        <th tabindex="3" rowspan="1" colspan="1">Q #4</th>
                                        <th tabindex="4" rowspan="1" colspan="1">Name</th>
                                        <th tabindex="5" rowspan="1" colspan="1">E-mail</th>
                                        <th tabindex="6" rowspan="1" colspan="1">Phone</th>
                                        <th tabindex="7" rowspan="1" colspan="1">User</th>
                                        <th tabindex="8" rowspan="1" colspan="1">Q1 Char… Count</th>
                                        <th tabindex="9" rowspan="1" colspan="1">Score</th>
                                        <th tabindex="10" rowspan="1" colspan="1">Action</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @foreach($surveyData as $row)
                                        <tr>
                                            <td>{{ $row->items[0]->a }}</td>
                                            <td>{{ $row->items[1]->a }}</td>
                                            <td>{{ $row->items[2]->a }}</td>
                                            <td>{{ $row->items[3]->a }}</td>
                                            <td>{{ $row->name }}</td>
                                            <td>{{ $row->email }}</td>
                                            <td>{{ $row->phone }}</td>
                                            <td>{{ \App\Models\User::find($row->user_id) ? \App\Models\User::find($row->user_id)->first()->email : '' }}</td>
                                            <td><strong>{{ mb_strlen($row->items[0]->a) }}</strong></td>
                                            <td>
                                                <strong>
                                                    @if(!empty($row->phone))
                                                        {{ mb_strlen($row->items[0]->a) * 1.5 }}
                                                    @else
                                                        {{ mb_strlen($row->items[0]->a) }}
                                                    @endif
                                                </strong>
                                            </td>
                                            <td>
                                                <a href="#" data-href="{{ route('survey.delete', $row->id) }}" class="btn btn-xs btn-default js-delete-survey">
                                                    <i class="fa fa-trash"></i>
                                                    Delete
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>

                                <tfoot>
                                    <tr role="row">
                                        <th tabindex="0" rowspan="1" colspan="1">Q #1</th>
                                        <th tabindex="1" rowspan="1" colspan="1">Q #2</th>
                                        <th tabindex="2" rowspan="1" colspan="1">Q #3</th>
                                        <th tabindex="3" rowspan="1" colspan="1">Q #4</th>
                                        <th tabindex="4" rowspan="1" colspan="1">Name</th>
                                        <th tabindex="5" rowspan="1" colspan="1">E-mail</th>
                                        <th tabindex="6" rowspan="1" colspan="1">Phone</th>
                                        <th tabindex="7" rowspan="1" colspan="1">User</th>
                                        <th tabindex="8" rowspan="1" colspan="1">Q1 Char… Count</th>
                                        <th tabindex="9" rowspan="1" colspan="1">Score</th>
                                        <th tabindex="10" rowspan="1" colspan="1">Action</th>
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

@section('after_styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.13/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.2.4/css/buttons.dataTables.min.css">
@endsection

@section('after_scripts')
    <script src="https://cdn.datatables.net/1.10.13/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.2.4/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.2.4/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.2.4/js/buttons.flash.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.2.4/js/buttons.print.min.js"></script>

    <script>
        jQuery(document).ready(function($) {
            $('#surveyTable').DataTable({
                dom: 'Bfrtip',
                buttons: [
                    'csv', 'pdf', 'print'
                ]
            });

            $('body').on('click', '.js-delete-survey', function(e) {
                e.preventDefault();

                var el = $(this);
                var token = '{{ csrf_token() }}';
                var href = el.data('href');

                $.ajax({
                    url: href,
                    type: 'post',
                    data: {_method: 'delete', _token :token},
                    success:function(msg)
                    {
                        if(msg.status)
                        {
                            el.closest('tr').remove();
                        }
                    }
                });
            });
        });
    </script>

    <style>
        #surveyTable tr.even {
            background: #ececec !important;
        }
    </style>
@endsection