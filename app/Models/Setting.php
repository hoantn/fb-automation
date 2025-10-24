<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class Setting extends Model
{
    protected $table = 'settings';
    protected $fillable = ['key', 'type', 'value', 'value_json'];
    public $timestamps = true;

    protected static function detectType($v): string
    {
        if (is_bool($v)) return 'bool';
        if (is_int($v)) return 'int';
        if (is_float($v)) return 'float';
        if (is_array($v) || is_object($v)) return 'json';
        return 'string';
    }

    public static function get(string $key, $default = null)
    {
        $row = static::where('key', $key)->first();
        if (!$row) return $default;

        if (Schema::hasColumn('settings', 'value_json') && !is_null($row->value_json)) {
            $decoded = json_decode($row->value_json, true);
            if (json_last_error() === JSON_ERROR_NONE) return $decoded;
        }

        if (!is_null($row->value) && is_string($row->value)) {
            $decoded = json_decode($row->value, true);
            if (json_last_error() === JSON_ERROR_NONE) return $decoded;
        }

        return $row->value ?? $default;
    }

    public static function set(string $key, $value): void
    {
        $payload = [
            'type'       => static::detectType($value),
            'value'      => null,
            'value_json' => null,
        ];

        if (is_array($value) || is_object($value)) {
            $payload['value_json'] = json_encode($value, JSON_UNESCAPED_UNICODE);
        } else {
            $payload['value'] = is_null($value) ? null : (string) $value;
        }

        static::updateOrCreate(['key' => $key], $payload);
    }
}
