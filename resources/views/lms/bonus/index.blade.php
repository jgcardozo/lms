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
                        <h2 class="single-header-block__title ucase">Lorem ipsum title</h2>
                        <p class="single-header-block__content">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Assumenda doloribus hic ipsa ipsum laboriosam nesciunt quae quia quos saepe, tenetur.</p>
                        <div class="single-header-block__separator"></div>
                        <div class="single-header-block__content single-header-block__content--small">
                            Lorem ipsum dolor sit amet, consectetur adipisicing elit. Autem earum exercitationem facilis non repudiandae. Architecto beatae.
                        </div>                        
                    </div>

                    <div class="single-header-video">
                         <div class="wistia_responsive_padding">
                             <div class="wistia_responsive_wrapper">
                                 <div class="wistia_embed wistia_async_teyda95d52"></div>
                             </div>
                         </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid--w950">
            <div class="course-modules">
                <h2 class="course-modules__title">Lorem ipsum sit amet</h2>

                <div class="grid--flex course-modules__list flex--wrap">
                    @foreach($bonuses as $bonus)
                        <div id="bonus-{{ $bonus->id }}" class="module module--push-b grid--flex">
                            <div class="module__component grid--flex flex--column">
                                <div class="module__featured-image" @if($bonus->featured_image) style="background-image: url({{ $bonus->featured_image_url }});" @endif>
                                </div>

                                <div class="module__content">
                                    <h2 class="module__title">{{ $bonus->title }}</h2>

                                    <p>{{ truncate_string($bonus->description) }}</p>

                                    <a href="{{ route('single.bonus', $bonus->slug) }}" class="module__link">Go To Bonus</a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </main>
@endsection