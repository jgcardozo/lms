@foreach($askAlert as $_askAlert)
    <div class="ask-alert ask-alert--full ask-alert--full--{{ $_askAlert['status'] }}" data-key="{{ route('alert.view', $_askAlert['key']) }}">
        {!! $_askAlert['message'] !!}
        <a href="#" class="js-close-ask-alert"></a>
    </div>
@endforeach