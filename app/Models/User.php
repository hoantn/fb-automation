<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $guarded = [];

    public function pages()
    {
        return $this->belongsToMany(Page::class, 'page_user')
            ->withPivot('role')
            ->withTimestamps();
    }
}
