@extends('backpack::layout')

@section('header')
    <section class="content-header">
        <h1>
            Notify users
        </h1>
    </section>
@endsection

@section('content')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/css/select2.css" type="text/css">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-default">
                <div class="box-header with-border">
                    <div class="box-title">Send notification to users</div>
                </div>

                <form method="post" action="{{ route('notify.send') }}" id="formNotification">
                    <div class="box-body">
                        <div class="form-group">
                            <label class="radio-inline"><input type="radio" value="all" name="optradio">To All Users</label>
                            <label class="radio-inline"><input type="radio" value="user" name="optradio">Specific User</label>
                            <label class="radio-inline"><input type="radio" value="cohort_course" name="optradio" checked>Cohort/Course</label>
                        </div>

                        <div class="form-group cohort_course">
                            <label>Send notifications only to users in course: <br/> <small>If there you select a course, and send a message to all users</small></label>
                            <select multiple="" name="courses[]" class="form-control" id="courses" style="min-height: 150px;">
                                @foreach($courses as $course)
                                    <option value="{{ $course->id }}">{!! $course->title !!}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group cohort_course">
                            <label>Send notifications only to users in cohort:</label>
                            <select multiple="" name="cohorts[]" class="form-control" id="cohorts" style="min-height: 150px;">
                                @foreach($cohorts as $cohort)
                                    <option value="{{ $cohort->id }}">{!! $cohort->name !!}</option>
                                @endforeach
                            </select>
                        </div>
                        <p id="requiredCohortCourse" style="display: none; color: red">*At least one cohort or course is required.</p>

                        <div class="form-group users" style="display: none">
                            <label for="users">Send notifications to specific users: <br/></label>
                            <select multiple="multiple" name="users[]" id="users" class="form-control" style="min-height: 150px;">
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}">{!! $user->name !!} - {!! $user->email !!}</option>
                                @endforeach
                            </select>
                        </div>
                        <p id="requiredUsers" style="display: none; color: red">*At least one user required.</p>

                        <div class="form-group">
                            <label>Notification message</label>
                            <textarea name="message" class="form-control ckeditor"></textarea>
                        </div>
                        <p id="requiredMessage" style="display: none; color: red">*A message is required.</p>
                    </div>

                    <div class="box-footer">
                        <button type="submit" class="btn btn-primary" id="btnNotificationSubmit">Submit</button>
                    </div>

                    {{ csrf_field() }}
                </form>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="box box-default">
                <div class="box-header with-border">
                    <div class="box-title">Logs of notifications sent</div>
                </div>
                <div class="box-body">
                    <table class="table" id="logTable">
                        @foreach($logs as $log)
                            <tr>
                                <td>
                                   <b>{{ $log->user->name }}</b> sent a notification to
                                        <b>
                                            @if($log->subject['type'] === "specificUsers")
                                                Specific Users
                                            @elseif($log->subject['type'] === "cohortCourse")
                                                Cohort/Course
                                            @else
                                                All Users
                                            @endif
                                        </b>
                                        with a message <b>{{ strip_tags($log->message) }}</b>
                                </td>
                                <td>
                                    <form method="post" action="{{ route('notification.log.delete',$log->id) }}">
                                        {{ method_field('delete') }}
                                        {{ csrf_field() }}
                                        <button class="btn btn-primary" value="{{ $log->id  }}">Delete</button>
                                    </form>
                                </td>
                                <td>
                                    @if(is_array($log->subject))
                                        @if($log->subject['type'] !== "All users")
                                            <button type="button" class="btn btn-info" data-toggle="modal" data-target="#detailsModal{{$log->id}}">View Details</button>
                                        @endif
                                    @else
                                        {{ $log->subject }}
                                    @endif
                                </td>

                                <!-- Modal -->
                                <div id="detailsModal{{$log->id}}" class="modal fade" role="dialog">
                                    <div class="modal-dialog">

                                        <!-- Modal content-->
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                <h2 class="modal-title">Sent to:</h2>
                                            </div>
                                            <div class="modal-body">
                                                @if($log->subject['type'] === "cohortCourse")
                                                    @include('lms.notifications.partials.courseDetails')
                                                @elseif($log->subject['type'] === "specificUsers")
                                                    @include('lms.notifications.partials.usersDetails')
                                                @endif
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </tr>
                        @endforeach
                    </table>

                    {{$logs->fragment('logTable')->links()}}
                </div>
            </div>
        </div>
    </div>
@endsection

@section('after_scripts')
    <script src="{{ asset('vendor/backpack/ckeditor/ckeditor.js') }}"></script>
    <script src="{{ asset('vendor/backpack/ckeditor/adapters/jquery.js') }}"></script>
    <script src="{{ asset('vendor/adminlte/plugins/select2/select2.min.js') }}"></script>
    <script>
            jQuery(document).ready(function($) {
                    $('textarea[name="message"].ckeditor').ckeditor({
                        "filebrowserBrowseUrl": "{{ url(config('backpack.base.route_prefix').'/elfinder/ckeditor') }}",
                        "extraPlugins" : '{{ isset($field['extra_plugins']) ? implode(',', $field['extra_plugins']) : 'oembed,widget' }}'
                    });

                $("input[type=radio][name=optradio]").change(function(){
                    var inp = this;
                    if(inp.value === "user") {
                        $(".users").css('display','block');
                        $(".cohort_course").css('display','none');
                    }
                    if(inp.value === "cohort_course") {
                        $(".users").css('display','none');
                        $(".cohort_course").css('display','block');
                    }
                    if(inp.value === "all") {
                        $(".users").css('display','none');
                        $(".cohort_course").css('display','none');
                    }

                    $("#requiredCohortCourse, #requiredMessage, #requiredUsers").css('display','none');
                });

                $('#users').select2({
                    width : "100%",
                    display : 'block',
                    theme: 'classic'
                });
            });

            $('#btnNotificationSubmit').click(function (e) {
                e.preventDefault();

                $("#requiredCohortCourse, #requiredMessage, #requiredUsers").css('display','none');

                var submit = true;

                var mode = $("input[type=radio][name=optradio]:checked").val();

                if(mode === "user") {
                    var countUsers = $("#users :selected").length;

                    if(countUsers === 0) {
                        submit = false;
                        $("#requiredUsers").css("display","block");
                    }
                }
                if(mode === "cohort_course") {
                    var countCourses = $("#courses :selected").length;
                    var countCohorts = $("#cohorts :selected").length;

                    if(countCohorts === 0 && countCourses === 0)
                    {
                        submit = false;
                        $("#requiredCohortCourse").css("display","block");
                    }
                }

                if(CKEDITOR.instances.message.getData().length === 0)
                {
                    submit = false;
                    $("#requiredMessage").css("display","block");
                }

                if(submit)
                {
                    $("#formNotification").submit();
                }
            })
    </script>
@endsection
