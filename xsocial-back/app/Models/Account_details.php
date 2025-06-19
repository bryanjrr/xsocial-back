<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Account_details extends Model
{

    protected $fillable = [
        'full_name',
        'phone',
        'birthdate',
        'location',
        'biography',
        'user_id'
    ];
}
