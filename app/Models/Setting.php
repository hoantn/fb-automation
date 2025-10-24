<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class Setting extends Model
{
    protected $table = 'settings';
    public $timestamps = true;

    // Cho phép cả 2 cột để tương thích 2 schema
    protected $fillable = ['key', 'value', 'value_json'];

    /**
     * Lấy setting theo key. Tự nhận biết cột đang dùng.
     */
    public static function get(string $key, $default = null)
    {
        $row = static::where('key', $key)->first();
        if (!$row) {
            return $default;
        }

        // Ưu tiên value_json nếu có
        if (Schema::hasColumn('settings', 'value_json')) {
            $raw = $row->value_json;
        } else {
            $raw = $row->value;
        }

        if (is_string($raw)) {
            $decoded = json_decode($raw, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                return $decoded;
            }
        }
        return $raw ?? $default;
    }

    /**
     * Ghi setting theo key. Tự nhận biết cột đang dùng (value / value_json).
     */
    public static function set(string $key, $value): void
    {
        $payload = [];

        if (Schema::hasColumn('settings', 'value_json')) {
            $payload['value_json'] = is_string($value)
                ? $value
                : json_encode($value, JSON_UNESCAPED_UNICODE);
        }

        if (Schema::hasColumn('settings', 'value')) {
            $payload['value'] = (is_array($value) || is_object($value))
                ? json_encode($value, JSON_UNESCAPED_UNICODE)
                : $value;
        }

        static::updateOrCreate(['key' => $key], $payload);
    }
}
