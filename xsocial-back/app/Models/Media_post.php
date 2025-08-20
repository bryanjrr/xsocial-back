<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Media_post extends Model
{
    protected $fillable = [
        'file_url'
    ];

    public function media_post()
    {
        return $this->morphTo();
    }
}
