<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - @yield('title')</title>

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

    <!-- Scripts -->
    <script>
        window.Laravel = {!! json_encode([
            'csrfToken' => csrf_token(),
        ]) !!};
    </script>

    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/favicons/apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" href="{{ asset('images/favicons/favicon-32x32.png') }}" sizes="32x32">
    <link rel="icon" type="image/png" href="{{ asset('images/favicons/favicon-16x16.png') }}" sizes="16x16">
    <link rel="manifest" href="{{ asset('images/favicons/manifest.json') }}">
    <link rel="mask-icon" href="{{ asset('images/favicons/manifest.json') }}" color="#5bbad5">

    <!-- Loading template -->
    <script id="loading-template" type="text/template">
        <div class="loading">
            <div class="loading__spinner"></div>
        </div>
    </script>

    <script src="https://use.typekit.net/izh8egw.js"></script>
    <script>try{Typekit.load({ async: true });}catch(e){}</script>

    <!-- Google Tag Manager -->
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
            new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
            j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
            'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
    })(window,document,'script','dataLayer','GTM-PQK7FV');</script>
    <!-- End Google Tag Manager -->
</head>

@if(changeHeader())
    <body>
@elseif(is_home())
    <body class="home">
@else
    <body class="inner-page">
@endif
        <!-- Google Tag Manager (noscript) -->
        <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-PQK7FV" height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
        <!-- End Google Tag Manager (noscript) -->

        <div id="app">
            @include('layouts.header')
            @include('layouts.mobile-menu')

            @yield('content')

            @include('layouts.footer')
        </div>

        @yield('scripts_before')

        <!-- Scripts -->
        <script src="{{ asset('js/app.js') }}"></script>

        <script>!function(e,o,n){window.HSCW=o,window.HS=n,n.beacon=n.beacon||{};var t=n.beacon;t.userConfig={},t.readyQueue=[],t.config=function(e){this.userConfig=e},t.ready=function(e){this.readyQueue.push(e)},o.config={docs:{enabled:!1,baseUrl:""},contact:{enabled:!0,formId:"0a256aba-28cc-11e7-9841-0ab63ef01522"}};var r=e.getElementsByTagName("script")[0],c=e.createElement("script");c.type="text/javascript",c.async=!0,c.src="https://djtflbt20bdde.cloudfront.net/",r.parentNode.insertBefore(c,r)}(document,window.HSCW||{},window.HS||{});</script>
        <script>
            HS.beacon.ready(function() {
                HS.beacon.identify({
                    name: '{{ Auth::user()->name }}',
                    email: '{{ Auth::user()->email }}'
                });
            });
        </script>

        @yield('scripts_after')
    </body>
</html>
