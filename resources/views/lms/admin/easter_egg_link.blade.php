@extends('backpack::layout')

@section('header')
    <section class="content-header">
        <h1>
            <span class="text-capitalize">Easter Egg Links</span>
            <small>{{ trans('backpack::crud.edit') }}
                <span>Easter Egg Links</span> {{ trans('backpack::crud.in_the_database') }}.
            </small>
        </h1>
        <ol class="breadcrumb">
            <li>
                <a href="{{ url(config('backpack.base.route_prefix'), 'dashboard') }}">{{ trans('backpack::crud.admin') }}</a>
            </li>
            {{--<li><a href="{{ url($crud->route) }}" class="text-capitalize">{{ $crud->entity_name_plural }}</a></li>--}}
            {{--<li class="active">{{ trans('backpack::crud.edit') }}</li>--}}
        </ol>
    </section>
@endsection

@section('content')


    <!-- Default box -->
    <div class="row">

        <!-- THE ACTUAL CONTENT -->
        <div class="col-md-8 col-md-offset-2">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">Edit</h3>
                </div>
                <div class="box-body row display-flex-wrap">
                    <form action="{{ route('easter_links.store') }}" method="POST" style="margin: 15px">
                        {{ csrf_field() }}
                        <div class="form-group">
                            <label for="cohorts">Cohort</label>
                            <select name="cohort" id="cohorts" class="form-control">
                                @foreach($cohorts as $cohort)
                                    <option value="{{ $cohort->id }}" @if(old('cohort') == $cohort->id) selected @endif>{{ $cohort->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-11 col-md-offset-1" id="modules_lessons">
                            @foreach($course->modules as $module)

                                <div class="panel panel-default">
                                    <div class="panel-heading"><b>{{ $module->title }}</b></div>
                                </div>

                                @foreach($module->lessons as $lesson)

                                        <div class="form-group">
                                            <div class="input-group col-md-offset-1">
                                                <span class="input-group-addon" id="basic-addon3"><b>Lesson</b>: {{ $lesson->title }}</span>
                                                <input type="text" class="form-control" name="lessons[{{ $lesson->id }}]" id="lessons_{{ $lesson->id }}" aria-describedby="basic-addon3" value=""   style="background-color: white">
                                            </div>
                                        </div>

                                @endforeach
                            @endforeach
                        </div>

                        <div class="form-group col-md-11 col-md-offset-1" id="courses">
                            <div class="panel panel-default">
                                <div class="panel-heading"><b>Courses</b></div>
                            </div>

                            @foreach($courses as $course)

                                <div class="form-group">
                                    <div class="input-group col-md-offset-1">
                                        <span class="input-group-addon" id="basic-addon3"><b>Course</b>: {!! $course->title !!}</span>
                                        <input type="text" class="form-control" name="courses[{{ $course->id }}]" id="courses_{{ $course->id }}" aria-describedby="basic-addon3" value=""   style="background-color: white">
                                    </div>
                                </div>

                            @endforeach
                        </div>

                        <div class="box-footer">
                            <div class="form-group">
                                <div class="btn-group">
                                    <button type="submit" class="btn btn-success">
                                        <span>Save and back</span>
                                    </button>
                                </div>
                                <a href="{{ url('/admin') }}" class="btn btn-default">
                                    <span class="fa fa-ban"></span> &nbsp;Cancel</a>
                            </div>
                        </div>


                    </form>
                </div>

            </div><!-- /.box -->
        </div>

    </div>
    <script src="https://code.jquery.com/jquery-2.2.0.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function () {

            $.get('/admin/easter_links/'+ $('#cohorts').val())
                .then(function (response) {
                    $('[id^="lessons"]').val('');
                    if(response && Object.keys(response).length) {
                        response.fbLinkLessons.forEach(function (link) {
                            $('#lessons_'+link.linkable_id).val(link.fb_link)
                        });
                        response.fbLinkCourses.forEach(function (link) {
                            $('#courses_'+link.linkable_id).val(link.fb_link)
                        })
                    }
                });

             $('#cohorts').on('change',function () {
                 $.get('/admin/easter_links/'+ $(this).val())
                     .then(function (response) {
                         $('[id^="lessons"]').val('');
                         if(response && Object.keys(response).length) {
                             response.fbLinkLessons.forEach(function (link) {
                                 $('#lessons_'+link.linkable_id).val(link.fb_link)
                             });
                             response.fbLinkCourses.forEach(function (link) {
                                 $('#courses_'+link.linkable_id).val(link.fb_link)
                             })
                         }
                     })
             })
        });
    </script>
@endsection