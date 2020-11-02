<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

    <!-- Scripts -->
    <script>
        window.Laravel = {!! json_encode([
            'csrfToken' => csrf_token(),
        ]) !!};
    </script>
</head>
<body>
    <div id="app">
        @include('lms.header')
        @yield('before_content')
        <div id="content">
            <div class="wrap">
                @yield('content')
            </div>
        </div>
        @yield('after_content')
    </div>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}"></script>
    <script defer>
        $(document).ready(function () {
            $('body').on('click', '.session-single__close', function (e) {
                var url = new URL(window.location.href);
                var params = new URLSearchParams(url.search.slice(1));
                params.delete("session");

                window.history.replaceState("", "", "?");

                console.log(params.toString(), "remove");
            });

            $('body').on('click', '.js-open-session', function (e) {
                var url = new URL(window.location.href);
                var params = new URLSearchParams(url.search.slice(1));
                params.append("session", $(e.target).data('session-id'));

                window.history.replaceState("", "", "?" + params.toString());

                console.log(params.toString(), "append");
            });
        })
    </script>
</body>
</html>
