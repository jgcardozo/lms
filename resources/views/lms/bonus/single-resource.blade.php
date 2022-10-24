@extends('layouts.app')

@section('title', $resource->title)

@section('scripts_before')
<script src="//fast.wistia.com/assets/external/E-v1.js" async></script>
@endsection


@section('content')

<link href="{{ asset('resources-bank/main.css') }}" rel="stylesheet">

<main class="resources-bank-page">



    <div class="intro" @if($resource->header_image) style="background-image: url({{ $resource->header_image_url }});" @else status="no-bg" @endif>
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


            @if (!$resource->published)
            <h4></h4>
            @else
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
            @endif




            <section class="content">
                <div class="">
                    <div class="course-modules">
                        <div class="course-modules__list">
                            @if (!$resource->published)
                            <h2>Resources Coming Soon!</h2>
                            <p>
                                The resources bank for the {!! $resource->title !!} are coming soon! Please check this page later to see them.
                            </p>
                            @else
                                @foreach ($sections as $child)
                                    @if ($child->published)    
                                        <article id="section-{!! $child->id !!}" class="item">
                                            <h2>{!! $child->title !!}</h2>
                                    
                                                <?php                   
                                                    $pieces = explode("[button", $child->content);
                                                    foreach ($pieces as $i=>$button) { 
                       
                                                        if (strpos($button, "[/button]</p>")!=""){
                                                            $button = "<p>[button".$button;
                                                        } elseif (strpos($button, "[/button]</td>")!=""){
                                                            $button = "[button".$button;
                                                                //echo $button; exit;
                                                        }   
                                                ?>
                                                    {!! compileShortcodes($button)!!}
                                                <?php  
                                                    } //foreach
                                                ?> 
                                                                                    
                                        </article>
									@else
										<article id="section-{!! $child->id !!}" class="item">
                                            <h2>{!! $child->title !!}</h2>
											<h5>Resources Coming Soon!</h5>
                                        </article>										
                                    @endif    
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>
            </section>

        </div>

    </section>

</main>

@endsection


@section('scripts_after')

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

<script>
    console.log("Hello world");

    //(function($) {
    $( document ).ready(function() {    
        function closeMenu() {
            //console.log("function called");
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


        //$('article p a').attr('target', '_blank'); now in helpers.php

    
    }); //jquery    
    //})(jQuery);
</script>


@endsection