<?php

namespace App\Models;



// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use App\Models\Account_details;

class User extends Authenticable
{
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */

    protected $primaryKey = 'id_user';

    protected $fillable = [
        'username',
        'email',
        'name',
        'surname',
        'password',
        'photo'
    ];

    public function account_details()
    {
        return $this->hasOne(Account_details::class, 'id_user');
    }

    public function followingUsers()
    {
        return $this->belongsToMany(User::class, 'user_followers', 'id_follower', 'id_followed')->withTimestamps();
    }

    public function followersUser()
    {
        return $this->belongsToMany(User::class, 'user_followers', 'id_followed', 'id_follower')->withTimestamps();
    }

    public function post()
    {
        return $this->morphMany(Post::class, 'posteable');
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }



    /* Relacion con Account_Details */
}
