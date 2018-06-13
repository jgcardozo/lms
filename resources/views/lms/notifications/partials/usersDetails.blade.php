<div class="box box-default">
    <div class="box-body">
        <div style="overflow-y: scroll; max-height: 300px">
            @foreach($log->subject['users'] as $user)
                <p>{{ $user->name }} - {{ $user->email }}</p>
                <hr style="height: 3px">
            @endforeach
        </div>
    </div>
</div>