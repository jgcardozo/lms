@extends('layouts.app')

@section('title', $resource->title)

@section('scripts_before')
<script src="//fast.wistia.com/assets/external/E-v1.js" async></script>
@endsection


@section('content')

<link href="{{ asset('resources-bank/main.css') }}" rel="stylesheet">

<main class="resources-bank-page">



    <div class="intro" @if($resource->header_image) style="background-image: url({{ $resource->header_image_url }});" @else status="no-bg" @endif;>
        <div class="course-single__overlay"></div>
        <div class="titles">
            <h1>{!! $resource->title !!}</h1>
            <p>
                {!! $resource->description !!}
            </p>
        </div>
    </div>

    <section class="white">

        <div class="resources-bank-page__container">

            <div class="menu--mobile">
                <button class="aside__menu"></button> Resources Index
            </div>


            <aside class="aside">
                <button class="aside__close"></button>
                <div class="aside__fixed">
                    <ul>
                        <li>
                            Page Index
                        </li>
                        @foreach ($sections as $child)
                        <li>
                            <a href="#section-{!! $child->id !!}">
                                {!! $child->title !!}
                            </a>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </aside>



            <section class="content">
                <div class="">
                    <div class="course-modules">
                        <div class="course-modules__list">
                            @if (!$resource->published)
                            <h2>Resource Coming Soon!</h2>
                            @else
                            @foreach ($sections as $child)

                            <article id="section-{!! $child->id !!}" class="item">
                                <h2>{!! $child->title !!}</h2>
                                {!! compileShortcodes($child->content)!!}
                            </article>

                            @endforeach
                            @endif
                        </div>
                    </div>
                </div>
            </section>

        </div>

    </section>

</main>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

<script>
    console.log("Hello world");

    (function($) {
        function closeMenu() {


            console.log("function called");

            $(".aside").removeClass("aside--active");
            $("body").css("overflow", "visible");
        }

        $(".aside__nav-item").click(function() {
            $(".aside__nav-item").removeClass("aside__nav-item--active");
            $(this).addClass("aside__nav-item--active");

            var toElem = $(this).attr("data-offset");
            var wScreen = $(window).width();
            var offsetValue = wScreen < 900 ? 80 : 60;

            $([document.documentElement, document.body]).animate({
                scrollTop: $("#" + toElem).offset().top - offsetValue
            }, 500);

            closeMenu();
        });

        $(".aside__menu").on("click", function() {
            $(".aside").addClass("aside--active");
            $("body").css("overflow", "hidden");
        });

        $(".aside__close").on("click", function() {
            closeMenu();
        });
    })(jQuery);
</script>




@endsection