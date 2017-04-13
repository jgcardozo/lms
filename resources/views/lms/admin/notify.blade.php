@extends('backpack::layout')

@section('header')
    <section class="content-header">
        <h1>
            Notify users
        </h1>
    </section>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="box box-default">
                <div class="box-header with-border">
                    <div class="box-title">Send notification to users</div>
                </div>

                <form method="post" action="{{ route('notify.send') }}">
                    <div class="box-body">
                        <div class="form-group">
                            <label>Send notifications only to users in course: <br/> <small>If there you select a course, and send a message to all users</small></label>
                            <select multiple="" name="courses[]" class="form-control" style="min-height: 150px;">
                                @foreach($courses as $course)
                                    <option value="{{ $course->id }}">{{ $course->title }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Notification message</label>
                            <textarea name="message" class="form-control"></textarea>
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