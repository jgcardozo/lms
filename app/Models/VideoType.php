<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VideoType extends Model
{
    const WISTIA = 1;
    const YOUTUBE = 2;
    const VIMEO = 3;
}
