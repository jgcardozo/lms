@if(!is_null($log->subject['courses']))
    @foreach($log->subject['courses'] as $course)
        <p><b>Course: </b>{{  $course->title }}</p>
        <hr>
    @endforeach
@endif
@if(!is_null($log->subject['cohorts']))
    @foreach($log->subject['cohorts'] as $cohort)
        <p><b>Cohort: </b>{{ $cohort->name }}</p>
        <hr>
    @endforeach
@endif