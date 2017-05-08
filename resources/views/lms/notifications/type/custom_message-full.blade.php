<div id="n-{{ $notification->id }}" class="allnotifications__item {{ is_null($notification->read_at) ? 'allnotifications__item--unread' : '' }}">
    {!! $notification->data['message'] !!}

    <span class="masthead__notifications-list__item__time">{{ $notification->created_at->diffForHumans() }}</span>
</div>

@if(request()->has('id') && request()->get('id') == $notification->id)
    {{ $notification->markAsRead() }}
    @section('scripts_after')
        <script>
            $(document).ready( function() {
                $('body').find('#n-{{ $notification->id }}').addClass('allnotifications__item--current');
            });
        </script>
    @endsection
@endif