<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = ['key','value'];
    public $timestamps = false;
    protected $casts = ['value' => 'array'];

    public static function get(string $key, $default=null)
    {
        $row = static::where('key',$key)->first();
        if (!$row) return $default;
        return $row->value ?? $default;
    }

    public static function set(string $key, $value): void
    {
        static::updateOrCreate(['key'=>$key], ['value'=>$value]);
    }
}
