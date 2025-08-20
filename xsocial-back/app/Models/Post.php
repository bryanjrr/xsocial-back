<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $fillable = [
        'content'
    ];

    public function thread()
    {
        return $this->belongsTo(Thread::class, 'thread_id');
    }

    public function posteable()
    {
        return $this->morphTo();
    }


    public function media_post()
    {
        return $this->morphMany(Media_post::class, 'media');
    }
}
