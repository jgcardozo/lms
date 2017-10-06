@if(!empty($notifications['data']) && $notifications['count_unread'] > 0)
    <div class="popup-notification">
        <div class="popup-notification__content">
            <h3 class="popup-notification__content__message">{!! truncate_string($notifications['data']->first()->data['message'], 8) !!}</h3>

            <a href="#" class="popup-notification__content__button js-close-popup-notification js-notifications-mark-as-read" data-route="{{ route('notifications.markAsRead') }}">Ok! :)</a>

            @if($notifications['count_unread'] > 1)
                <p class="popup-notification__content__small-note"><a href="{{ route('notifications') }}">You have more unread notifications</a></p>
            @endif
        </div>
    </div>
@endif