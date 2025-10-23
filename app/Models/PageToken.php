<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PageToken extends Model
{
    protected $table = 'page_tokens';

    protected $fillable = [
        'page_id',        // fk -> pages.id
        'access_token',   // text
        'scopes',         // json|text
        'issued_by_user_id', // nullable int
        'status',         // string, e.g. 'active'
        'expires_at',     // datetime|null
    ];
}
