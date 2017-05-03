<div class="ask-alert ask-alert--full ask-alert--full--{{ $askAlert['status'] }}" data-key="{{ route('alert.view', $askAlert['key']) }}">
    {!! $askAlert['message'] !!}
    <a href="#" class="js-close-ask-alert"></a>
</div>