<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PageToken extends Model
{
    protected $fillable = [
        'page_id','access_token','scopes','issued_by_user_id','status','expires_at'
    ];

    protected $casts = [
        'scopes' => 'array',
        'expires_at' => 'datetime',
    ];

    public function page()
    {
        return $this->belongsTo(Page::class);
    }
}
