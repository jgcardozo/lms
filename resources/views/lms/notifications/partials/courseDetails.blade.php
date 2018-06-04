@if(!is_null($log->subject['courses']))
    @foreach($log->subject['courses'] as $course)
        {{ \App\Models\Course::find($course)->title }}
    @endforeach
@endif
@if(!is_null($log->subject['cohorts']))
    @foreach($log->subject['cohorts'] as $cohort)
        {{ \App\Models\Cohort::find($cohort)->name }}
    @endforeach
@endif