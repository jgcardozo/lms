<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationLog extends Model
{

    protected $fillable = [
        'user_id',
        'uuid',
        'subject',
        'message'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function setSubjectAttribute($value)
    {
        $this->attributes['subject'] = serialize($value);
    }

    public function getSubjectAttribute($value)
    {
        return unserialize($value);
    }
}
