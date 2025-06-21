<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Account_details extends Model
{

    protected $primaryKey = 'id';

    protected $fillable = [
        'id_user',
        'full_name',
        'phone',
        'birthdate',
        'location',
        'biography',

    ];
}
