@extends('layouts.app')

@section('title', 'Bonus')

@section('scripts_before')
    <script src="//fast.wistia.com/assets/external/E-v1.js" async></script>
@endsection

@section('content')
    <main>
        <div class="grid grid--full course-single">
            <div class="course-single__overlay"></div>

            <div class="grid grid--w950 course-single__content">
                <div class="course-single__content-wrap grid--flex flex--space-between">
                    <div class="single-header-block">
                        <h2 class="single-header-block__title ucase">Bonus Area</h2>
                        <!-- <p class="single-header-block__content">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Assumenda doloribus hic ipsa ipsum laboriosam nesciunt quae quia quos saepe, tenetur.</p>
                                                    <div class="single-header-block__separator"></div>
                                                    <div class="single-header-block__content single-header-block__content--small">
                                                        Lorem ipsum dolor sit amet, consectetur adipisicing elit. Autem earum exercitationem facilis non repudiandae. Architecto beatae.
                                                    </div>                         -->
                    </div>

                    <!-- <div class="single-header-video">
                                                     <div class="wistia_responsive_padding">
                                                         <div class="wistia_responsive_wrapper">
                                                             <div class="wistia_embed wistia_async_teyda95d52"></div>
                                                         </div>
                                                     </div>
                                                </div> -->
                </div>
            </div>
        </div>

        <div class="grid " style="display:flex; max-width: 100rem; padding-left: 1rem; padding-right: 1rem;">
            <div class="course-modules" style="">
                <!-- <h2 class="course-modules__title">Lorem ipsum sit amet</h2> -->

                <div class="grid--flex course-modules__list flex--wrap mobile" style="justify-content:center; ">

{{--                     @foreach ($collection as $item)
                        <?php//echo 'type:' . $item->type . ' title:' . $item->title . ' lft:' . $item->lft . ' created_at:' . $item->created_at ?><br>
                    @endforeach --}}
                    @forelse ($collection as $item)
                        @if ($item->type == 'resource')
                            <div id="resource-{{ $item->id }}" class=" module--push-b grid--flex"
                                style="width:310px; box-shadow: 0 3px 5px 0 rgba(0,0,0,0.2); border-radius:10px; padding:0px; margin:10px 6px; overflow:hidden;">
                                <div class="module__component grid--flex flex--column">
                                    <div class="module__featured-image"
                                        @if ($item->featured_image) style="background-image: url({{ $item->featured_image_url }});" @endif>
                                    </div>

                                    <div class="module__content">
                                        <h2 class="module__title">{{ $item->title }}
                                            @if (!$item->published)
                                                - Coming Soon!
                                            @endif
                                        </h2>

                                        <p style="color:#928780; font-size:1.4rem;">{!! truncate_string($item->description) !!}</p>
                                    </div>
                                    <div style="text-align:center; padding-bottom: 25px">
                                        <a href="{{ route('single.resourcesbank', $item->slug) }}" class="module__link">Go
                                            To
                                            Resource</a>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div id="bonus-{{ $item->id }}" class="module--push-b grid--flex"
                                style="width:310px; box-shadow: 0 3px 5px 0 rgba(0,0,0,0.2); border-radius:10px; padding:0px; margin:7px 4px; overflow:hidden;">
                                <div class="module__component grid--flex flex--column">
                                    <div class="module__featured-image"
                                        @if ($item->featured_image) style="background-image: url({{ $item->featured_image_url }});" @endif>
                                    </div>

                                    <div class="module__content">
                                        <h2 class="module__title">{{ $item->title }}</h2>

                                        <p style="color:#928780; font-size:1.4rem;">{!! truncate_string($item->description) !!}</p>
                                    </div>
                                    <div style="text-align:center; padding-bottom: 25px">
                                        <a href="{{ route('single.bonus', $item->slug) }}" class="module__link">Go To
                                            Bonus</a>
                                    </div>
                                </div>
                            </div>
                        @endif

                    @empty
                        <div class="card">
                            <div class="card-body">
                                <h2>It seems you don´t have any Bonus.</h2>
                            </div>
                        </div>
                    @endforelse

                </div>
            </div>
        </div>
    </main>
@endsection



@section('scripts_after')


    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

    <script>
        console.log("Hello from lms.bonus.index");

        $(document).ready(function() {

        }); //end-jquery-ready
    </script>
@endsection
