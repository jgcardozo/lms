<div class="notifications-list__item {{ is_null($notification->read_at) ? 'notifications-list__item--unread' : '' }}">
    {!! $notification->data['message'] !!}
</div>