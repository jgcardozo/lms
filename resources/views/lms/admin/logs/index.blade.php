@extends('layouts.app')

@section('content')
    <table border="1px solid" class="center-margin">
        @foreach($logs as $log)
            <tr>
                <td>{{ $log->id }}</td>
                <td>{{ $log->user->name }}</td>
                <td>{{ $log->action->name }}</td>
                @if(empty($log->activity))
                    <td>{{ $log->subject->title }}</td>
                @else
                    @if($log->activity->name === "Admin")
                        @if(empty($log->subject->title))
                            @if(empty($log->subject->name))
                                <td>{{ $log->subject_type}} <sup>here</sup></td>
                            @else
                                <td>{{ $log->subject->name}}</td>
                            @endif
                        @else
                            <td>{{ $log->subject->title }}</td>
                        @endif
                    @else
                        <td>{{ $log->activity->name }}</td>
                    @endif
                @endif
                <td>{{ $log }}</td>
            </tr>
        @endforeach
    </table>
@endsection