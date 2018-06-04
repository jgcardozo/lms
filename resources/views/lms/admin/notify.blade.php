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

                <form method="post" action="{{ route('notify.send') }}">
                    <div class="box-body">
                        <div class="form-group">
                            <label class="radio-inline"><input type="radio" value="all" name="optradio">To All Users</label>
                            <label class="radio-inline"><input type="radio" value="user" name="optradio">Specific User</label>
                            <label class="radio-inline"><input type="radio" value="cohort_course" name="optradio" checked>Cohort/Course</label>
                        </div>

                        <div class="form-group cohort_course">
                            <label>Send notifications only to users in course: <br/> <small>If there you select a course, and send a message to all users</small></label>
                            <select multiple="" name="courses[]" class="form-control" style="min-height: 150px;">
                                @foreach($courses as $course)
                                    <option value="{{ $course->id }}">{!! $course->title !!}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group cohort_course">
                            <label>Send notifications only to users in cohort:</label>
                            <select multiple="" name="cohorts[]" class="form-control" style="min-height: 150px;">
                                @foreach($cohorts as $cohort)
                                    <option value="{{ $cohort->id }}">{!! $cohort->name !!}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group users" style="display: none">
                            <label for="users">Send notifications to specific users: <br/></label>
                            <select multiple="multiple" name="users[]" id="users" class="form-control" style="min-height: 150px;">
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}">{!! $user->name !!} - {!! $user->email !!}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Notification message</label>
                            <textarea name="message" class="form-control ckeditor"></textarea>
                        </div>
                    </div>

                    <div class="box-footer">
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>

                    {{ csrf_field() }}
                </form>
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
                    if(inp.value == "user") {
                        $(".users").css('display','block');
                        $(".cohort_course").css('display','none');
                    }
                    if(inp.value == "cohort_course") {
                        $(".users").css('display','none');
                        $(".cohort_course").css('display','block');
                    }
                    if(inp.value == "all") {
                        $(".users").css('display','none');
                        $(".cohort_course").css('display','none');
                    }
                });

                $('#users').select2({
                    width : "100%",
                    display : 'block',
                    theme: 'classic'
                });
            });
    </script>
@endsection
