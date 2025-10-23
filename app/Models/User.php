<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'facebook_user_id',
        'avatar',
        'facebook_token',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'facebook_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function pages()
    {
        return $this->belongsToMany(Page::class)->withPivot('role')->withTimestamps();
    }
}
