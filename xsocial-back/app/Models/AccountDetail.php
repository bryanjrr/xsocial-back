<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AccountDetail extends Model
{
    use HasFactory;
    protected $primaryKey = 'id';

    protected $table = 'account_details';
    protected $fillable = [
        'id_user',
        'full_name',
        'phone',
        'birthdate',
        'location',
        'biography',
        'union_date'
    ];

    public function user_followers()
    {
        return $this->belongsToMany(User::class, 'user_followers', 'id_user', 'id_follower')->withTimestamps();
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }
}
