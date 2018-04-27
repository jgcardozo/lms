@extends('backpack::layout')

@section('header')
    <section class="content-header">
        <h1>
            <span class="text-capitalize">{{ $crud->entity_name_plural }}</span>
            <small>{{ trans('backpack::crud.add') }}
                <span>{{ $crud->entity_name_plural }}</span> {{ trans('backpack::crud.in_the_database') }}.
            </small>
        </h1>
        <ol class="breadcrumb">
            <li>
                <a href="{{ url(config('backpack.base.route_prefix'), 'dashboard') }}">{{ trans('backpack::crud.admin') }}</a>
            </li>
            <li><a href="{{ url($crud->route) }}" class="text-capitalize">{{ $crud->entity_name_plural }}</a></li>
            <li class="active">{{ trans('backpack::crud.add') }}</li>
        </ol>
    </section>
@endsection

@section('content')
    <!-- Default box -->
    <div class="row">

        <div class="col-md-8 col-md-offset-2">
            <a href="http://ask.oo/admin/course"><i class="fa fa-angle-double-left"></i> Back to all
                <span>courses</span></a><br><br>
        </div>

        <!-- THE ACTUAL CONTENT -->
        <div class="col-md-8 col-md-offset-2">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">Add a new schedule</h3>
                </div>
                <div class="box-body row display-flex-wrap">
                    <form action="{{ route('crud.schedule.store') }}" method="POST" style="margin: 15px">
                        {{ csrf_field() }}
                        <div class="form-group">
                            <label for="name">Name</label>
                            <input type="text" class="form-control" id="name" name="name" placeholder="Name of schedule" required>
                        </div>
                        <div class="form-group">
                            <label for="course_id">Course</label>
                            <select class="form-control" id="course_id" name="course_id" required>
                                @foreach($courses->pluck('title','id')->toArray() as $key => $value)
                                    <option value="{{ $key }}"> {{ strip_tags($value) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="schedule_type">Schedule type</label>
                            <select class="form-control" id="schedule_type" name="schedule_type" required>
                                <option value="locked">Lock</option>
                                <option value="dripped">Drip</option>
                            </select>
                        </div>
                        <div class="form-group col-md-11 col-md-offset-1" id="modules_lessons">
                            {{--Content is being created dynamically with javascript--}}
                        </div>

                        <div class="box-footer">
                            <div class="form-group">
                                <div class="btn-group">
                                    <button type="submit" class="btn btn-success">
                                        <span>Save and back</span>
                                    </button>
                                </div>
                                <a href="http://ask.oo/admin/schedule" class="btn btn-default">
                                    <span class="fa fa-ban"></span> &nbsp;Cancel</a>
                            </div>
                        </div>


                    </form>
                </div>

            </div><!-- /.box -->
        </div>

    </div>
    <script src="{{ asset('js/schedule_create.js') }}" defer></script>
@endsection