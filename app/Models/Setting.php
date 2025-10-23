<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $table = 'settings';
    protected $fillable = ['key','value'];
    public $timestamps = false;

    public static function get(string $key, $default = null)
    {
        $row = static::where('key',$key)->first();
        return $row ? $row->value : $default;
    }

    public static function set(string $key, $value): void
    {
        // chuẩn hóa giá trị trước khi lưu
        if (is_bool($value))     $value = $value ? '1' : '0';
        elseif (is_array($value) || is_object($value)) $value = json_encode($value, JSON_UNESCAPED_UNICODE);

        static::updateOrCreate(['key'=>$key], ['value'=>(string)$value]);
    }
}
