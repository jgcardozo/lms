@extends('layouts.app')

@section('title', $resource->title)

@section('scripts_before')
    <script src="//fast.wistia.com/assets/external/E-v1.js" async></script>
@endsection

@section('content')
 
        <div>
           {!! $resource->sidebar_content !!}
        </div>
        
        <main>
            <div class="grid grid--full course-single" @if($resource->header_image) style="background-image: url({{ $resource->header_image_url }});" @endif>
                
                
                <div class="course-single__overlay"></div>
    
                <div class="grid grid--w950 course-single__content">
                    <div class="course-single__content-wrap grid--flex flex--space-between">
                        <div class="single-header-block">
                            <h2 class="single-header-block__title ucase">{!! bold_first_word($resource->title) !!}</h2>
                            <p class="single-header-block__content">{{ $resource->short_description }}</p>
                            <div class="single-header-block__separator"></div>
                            <div class="single-header-block__content single-header-block__content--small">
                                {!! $resource->description !!}
                            </div>                        
                        </div>
    
                        <div class="single-header-video">
                             <div class="wistia_responsive_padding">
                                 <div class="wistia_responsive_wrapper">
                                     @include('lms.components.video', ['model' => $resource])
                                 </div>
                             </div>
                        </div>
                    </div>
                </div>
            </div>
    
            <div class="grid grid--w950">
                <div class="course-modules">
                    <div class="course-modules__list">
                        {!! compileShortcodes($resource->content) !!}
                    </div>
                </div>
            </div>
        </main>
    </div>
@endsection