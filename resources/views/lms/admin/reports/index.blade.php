@extends('vendor.backpack.base.layout')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="box">
            <div class="box-header with-border"><h1>Graduation Reports</h1></div>
            <div class="box-body">
                <h3>Students that failed the test</h3>
                <a href="/admin/graduation-report/fail-test" target="_blank" class="btn btn-primary">Get Report</a>
                <hr />
                <h3>Students that finished the course but didn't take the test</h3>
                <a href="/admin/graduation-report/finished-course" target="_blank" class="btn btn-primary">Get Report</a>
            </div>
        </div>
    </div>
</div>

@endsection