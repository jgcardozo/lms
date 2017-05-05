<li class="masthead__notifications-list__item {{ is_null($notification->read_at) ? 'notifications-list__item--unread' : '' }}">
    <a href="{{ route('notifications') }}">{{ truncate_string($notification->data['message'], 8) }}</a>
</li>