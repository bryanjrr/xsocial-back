<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Thread extends Model
{
    protected $fillable = [
        'id_category',
        'status',
        'title',
        'content',
        'id_user',
        'id_language',
        'views',
        'replies',
        'last_reply_at',
        'image',
        'type',
        'tags'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'id_category');
    }

    public function language()
    {
        return $this->belongsTo(Language::class, 'id_language');
    }

    public function post()
    {
        return $this->morphMany(Post::class, 'posteable');
    }
}
