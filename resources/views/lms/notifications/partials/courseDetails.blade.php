<div class="box box-default">
    <div class="box-body">
        <div style="overflow-y: scroll; max-height: 300px">
            @if(!is_null($log->subject['courses']))
                @foreach($log->subject['courses'] as $course)
                    <p><b>Course: </b>{!! $course->title  !!}</p>
                    <hr>
                @endforeach
            @endif
            @if(!is_null($log->subject['cohorts']))
                @foreach($log->subject['cohorts'] as $cohort)
                    <p><b>Cohort: </b>{{ $cohort->name }}</p>
                    <hr>
                @endforeach
            @endif
        </div>
    </div>
</div>