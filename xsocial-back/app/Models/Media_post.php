<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Media_post extends Model
{

    protected $table = 'media_posts';
    protected $fillable = ['media_id', 'file_url', 'media_type', 'content_type'];


    public function media()
    {
        return $this->morphTo();
    }

    public function contentType()
    {
        return $this->belongsTo(ContentType::class, 'content_type');
    }
}
