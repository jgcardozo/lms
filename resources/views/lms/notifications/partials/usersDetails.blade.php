@foreach($log->subject['users'] as $user)
    <p>{{ $user->name }} - {{ $user->email }}</p>
    <hr>
@endforeach