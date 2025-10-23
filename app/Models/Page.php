<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    protected $fillable = ['meta_page_id', 'name', 'meta'];
    protected $casts = ['meta' => 'array'];

    public function members()
    {
        return $this->belongsToMany(User::class)->withPivot('role')->withTimestamps();
    }

    public function tokens()
    {
        return $this->hasMany(PageToken::class);
    }

    public function activeToken()
    {
        return $this->tokens()->where('status', 'active')->latest()->first();
    }
}
