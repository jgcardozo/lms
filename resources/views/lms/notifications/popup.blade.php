@role('Administrator')
@if(!empty($notifications['not_displayed']))
    <div class="popup-notification">
        <div class="popup-notification__content">
            <div class="session-single__close js-close-popup-notification"></div>
            <div class="popup-notification__content__message">{!! compileShortcodes($notifications['not_displayed']->first()->data['message']) !!}</div>

            @if($notifications['count_unread'] > 1)
                <p class="popup-notification__content__small-note"><a href="{{ route('notifications') }}">You have more unread notifications</a></p>
            @endif
        </div>
    </div>

    @foreach($notifications['not_displayed'] as $notification)
        <?php
            $notification->display_at = \Carbon\Carbon::now();
            $notification->save();
        ?>
    @endforeach
@endif
@endrole