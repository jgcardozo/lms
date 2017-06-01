@if(!empty($askAlert))
    @foreach($askAlert as $_askAlert)
        <div class="ask-alert ask-alert--full ask-alert--full--{{ $_askAlert['status'] }}" data-key="{{ route('alert.view', $_askAlert['key']) }}">
            {!! $_askAlert['message'] !!}

            @if(!isset($_askAlert['close']) || (isset($_askAlert['close']) && $_askAlert['close'] != false))
            	<a href="#" class="js-close-ask-alert"></a>
            @endif
        </div>
    @endforeach
@endif