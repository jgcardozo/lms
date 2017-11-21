@if((isset($video_type) && ($video_type == \App\Models\VideoType::WISTIA)) || (isset($model) && ($model->video_type->id == \App\Models\VideoType::WISTIA)))
    <div class="wistia_embed wistia_async_{{ isset($video_url) ? $video_url : $model->video_url }}"></div>
@endif

@if((isset($video_type) && ($video_type == \App\Models\VideoType::YOUTUBE)) || (isset($model) && ($model->video_type->id == \App\Models\VideoType::YOUTUBE)))
    <iframe class="wistia_embed" width="560" height="349" src="https://www.youtube.com/embed/{{ isset($video_url) ? $video_url : $model->video_url }}?rel=0&amp;showinfo=0" frameborder="0"></iframe>
@endif

@if((isset($video_type) && ($video_type == \App\Models\VideoType::VIMEO)) || (isset($model) && ($model->video_type->id == \App\Models\VideoType::VIMEO)))
    <iframe class="wistia_embed" src="https://player.vimeo.com/video/{{ isset($video_url) ? $video_url : $model->video_url }}?byline=0" width="640" height="360" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
@endif
