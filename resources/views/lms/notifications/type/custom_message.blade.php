<li style="cursor: pointer" class="masthead__notifications-list__item {{ is_null($notification->read_at) ? 'masthead__notifications-list__item--unread' : '' }}">
    <a href="{{ route('notifications', ['id' => $notification->id]) }}">{{ truncate_string($notification->data['message'], 8) }}</a>

    <span class="masthead__notifications-list__item__time">{{ $notification->created_at->diffForHumans() }}</span>
</li>