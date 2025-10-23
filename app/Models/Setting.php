<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'key','value_json','type','scope_type','scope_id','version','is_active','created_by'
    ];

    protected $casts = [
        'value_json' => 'array',
        'is_active' => 'boolean',
    ];
}
