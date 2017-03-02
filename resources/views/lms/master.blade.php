<!doctype html>

<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="description" content="Learning management system">
        <meta name="author" content="Codeart.mk">

        <title>ASK 2.0 - @yield('title')</title>
        <link href="{!! asset('lms/css/test.css') !!}" rel="stylesheet" type="text/css" />

        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
        <script src="{!! asset('lms/js/main.js') !!}"></script>

        <!--[if lt IE 9]>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/html5shiv/3.7.3/html5shiv.js"></script>
        <![endif]-->
    </head>

    <body>
        @include('lms.header')
        @yield('before_content')
        <div id="content">
            <div class="wrap">
                @yield('content')
            </div>
        </div>
        @yield('after_content')
    </body>
</html>