<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    protected $table = 'pages';

    protected $fillable = [
        'meta_page_id',
        'name',
        'access_token',
        'meta',
    ];

    protected $casts = [
        'meta' => 'array',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'page_user')
            ->withPivot('role')
            ->withTimestamps();
    }
}
