@extends('backpack::layout')

@section('header')
    <section class="content-header">
        <h1>
            <span class="text-capitalize">{{ $crud->entity_name_plural }}</span>
            <small>{{ trans('backpack::crud.edit') }}
                <span>{{ $crud->entity_name_plural }}</span> {{ trans('backpack::crud.in_the_database') }}.
            </small>
        </h1>
        <ol class="breadcrumb">
            <li>
                <a href="{{ url(config('backpack.base.route_prefix'), 'dashboard') }}">{{ trans('backpack::crud.admin') }}</a>
            </li>
            <li><a href="{{ url($crud->route) }}" class="text-capitalize">{{ $crud->entity_name_plural }}</a></li>
            <li class="active">{{ trans('backpack::crud.edit') }}</li>
        </ol>
    </section>
@endsection

@section('content')



    <!-- Default box -->
    <div class="row">

        <div class="col-md-8 col-md-offset-2">
            <a href="{{ url('/admin/schedule') }}"><i class="fa fa-angle-double-left"></i> Back to all
                <span>schedules</span></a><br><br>
        </div>

        <!-- THE ACTUAL CONTENT -->
        <div class="col-md-8 col-md-offset-2">
            <div class="box">
                <div class="box-header with-border">
                    <h3 class="box-title">Edit</h3>
                </div>
                <div class="box-body row display-flex-wrap">
                    <form action="{{ route('crud.schedule.update',$entry->id) }}" method="POST" style="margin: 15px">
                        {{ csrf_field() }}
                        {{ method_field('put') }}
                        <div class="form-group">
                            <label for="name">Name</label>
                            <input type="text" class="form-control" id="name" name="name" placeholder="Name of schedule" required value="{{ $entry->name }}">
                        </div>
                        <input type="hidden" id="course_id" value="{{ $entry->course_id }}">
                        @if($entry->status === "default")
                            <div class="form-group" hidden>
                                <label for="cohorts">Cohorts</label>
                                <select multiple class="form-control" id="cohorts" name="cohorts[]">
                                    @foreach($cohorts->where('course_id',$entry->course_id)->pluck('name','id') as $key => $value)
                                        @if($cohorts->find($key)->schedule_id === $entry->id)
                                            <option selected value="{{ $key }}"> {{ $value }}</option>
                                        @else
                                            <option value="{{ $key }}"> {{ $value }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group hidden">
                                <label for="schedule_type">Schedule type</label>
                                <select class="form-control" id="schedule_type" name="schedule_type">
                                    @if($entry->schedule_type === "dripped")
                                        <option value="dripped">Drip</option>
                                        <option value="locked">Lock</option>
                                    @else
                                        <option value="locked">Lock</option>
                                        <option value="dripped">Drip</option>
                                    @endif
                                </select>
                            </div>
                        @else
                            <div class="form-group">
                                <label for="cohorts">Cohorts</label>
                                <select multiple class="form-control" id="cohorts" name="cohorts[]" required>
                                    @foreach($cohorts->where('course_id',$entry->course_id)->pluck('name','id') as $key => $value)
                                        @if($cohorts->find($key)->schedule_id === $entry->id)
                                            <option selected value="{{ $key }}"> {{ $value }}</option>
                                        @else
                                            <option value="{{ $key }}"> {{ $value }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="schedule_type">Schedule type</label>
                                <select class="form-control" id="schedule_type" name="schedule_type" required>
                                    @if($entry->schedule_type === "dripped")
                                        <option value="dripped">Drip</option>
                                        <option value="locked">Lock</option>
                                    @else
                                        <option value="locked">Lock</option>
                                        <option value="dripped">Drip</option>
                                    @endif
                                </select>
                            </div>
                        @endif
                        <div class="form-group">
                            <label class="radio-inline"><input type="radio" value="yes" name="optradio">With Sessions</label>
                            <label class="radio-inline"><input type="radio" value="no" name="optradio" checked>Without Sessions</label>
                        </div>
                        <div class="form-group col-md-11 col-md-offset-1" id="modules_lessons">
                            <label>Modules and Lessons</label>
                            @foreach($courses->keyBy('id')->get($entry->course_id)->modules as $module)

                                    @if($entry->schedule_type === "dripped")
                                    <div class="form-group">
                                        <div class="input-group">
                                            <span class="input-group-addon" id="basic-addon3"><b>Module</b>: {{ $module->title }}</span>
                                                <input type="number" min="0" class="form-control" name="modules[{{ $module->id }}]" id="module_{{ $module->id }}'" aria-describedby="basic-addon3" value="{{ $module->getDripOrLockDays($entry->id) }}" required>
                                        </div>
                                    </div>
                                    @else
                                    <div class="form-group">
                                        <div class="input-group date dtp">
                                            <span class="input-group-addon" id="basic-addon3"><b>Module</b>: {{ $module->title }}</span>
                                            <input type="text" class="form-control" name="modules[{{ $module->id }}]" id="module_{{ $module->id }}" aria-describedby="basic-addon3" value="{{ $module->getDripOrLockDays($entry->id) }}" required style="background-color: white">
                                            <span class="input-group-addon">
                                                <span class="glyphicon glyphicon-calendar"></span>
                                            </span>
                                        </div>
                                    </div>
                                    @endif
                                @foreach($module->lessons as $lesson)
                                    @if($entry->schedule_type === "dripped")
                                        <div class="form-group">
                                            <div class="input-group col-md-offset-1">
                                                <span class="input-group-addon" id="basic-addon3"><b>Lesson</b>: {{ $lesson->title }}</span>
                                                <input type="number" min="0" class="form-control" name="lessons[{{ $lesson->id }}]" id="lessons_{{ $lesson->id }}'" aria-describedby="basic-addon3" value="{{ $lesson->getDripOrLockDays($entry->id) }}" required>
                                            </div>
                                        </div>
                                    @else
                                        <div class="form-group">
                                            <div class="input-group date dtp col-md-offset-1">
                                                <span class="input-group-addon" id="basic-addon3"><b>Lesson</b>: {{ $lesson->title }}</span>
                                                <input type="text" class="form-control" name="lessons[{{ $lesson->id }}]" id="lessons_{{ $lesson->id }}" aria-describedby="basic-addon3" value="{{ $lesson->getDripOrLockDays($entry->id) }}" required style="background-color: white">
                                                <span class="input-group-addon">
                                                    <span class="glyphicon glyphicon-calendar"></span>
                                                </span>
                                            </div>
                                        </div>
                                    @endif
                                        @foreach($lesson->sessions as $session)
                                            @if($entry->schedule_type === "dripped")
                                                <div class="form-group">
                                                    <div class="input-group col-md-offset-2" style="display: none;">
                                                        <span class="input-group-addon" id="basic-addon3"><b>Session</b>: {{ str_limit($session->title, 40) }}</span>
                                                        <input type="number" min="0" class="form-control" name="sessions[{{ $session->id }}]" id="sessions_{{ $session->id }}'" aria-describedby="basic-addon3" value="{{ $session->getDripOrLockDays($entry->id) }}">
                                                    </div>
                                                </div>
                                            @else
                                                <div class="form-group">
                                                    <div class="input-group date dtp col-md-offset-2" style="display: none;">
                                                        <span class="input-group-addon" id="basic-addon3"><b>Session</b>: {{ str_limit($session->title, 40) }}</span>
                                                        <input type="text" class="form-control" name="sessions[{{ $session->id }}]" id="sessions_{{ $session->id }}" aria-describedby="basic-addon3" value="{{ $session->getDripOrLockDays($entry->id) }}"  style="background-color: white">
                                                        <span class="input-group-addon">
                                                    <span class="glyphicon glyphicon-calendar"></span>
                                                </span>
                                                    </div>
                                                </div>
                                            @endif
                                        @endforeach
                                @endforeach
                            @endforeach
                        </div>

                        <div class="box-footer">
                            <div class="form-group">
                                <div class="btn-group">
                                    <button type="submit" class="btn btn-success">
                                        <span>Save and back</span>
                                    </button>
                                </div>
                                <a href="{{ url('/admin/schedule') }}" class="btn btn-default">
                                    <span class="fa fa-ban"></span> &nbsp;Cancel</a>
                            </div>
                        </div>


                    </form>
                </div>

            </div><!-- /.box -->
        </div>

    </div>
@endsection