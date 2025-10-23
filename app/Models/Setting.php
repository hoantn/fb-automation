<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class Setting extends Model
{
    protected $table = 'settings';
    protected $fillable = ['key','value','value_json','type'];
    public $timestamps = false;

    public static function get(string $key, $default = null)
    {
        $row = static::where('key',$key)->first();
        if (!$row) return $default;

        if (Schema::hasColumn('settings', 'value') && $row->value !== null) {
            return $row->value;
        }
        if (Schema::hasColumn('settings', 'value_json')) {
            $decoded = json_decode($row->value_json, true);
            return json_last_error() === JSON_ERROR_NONE ? $decoded : $row->value_json;
        }
        return $default;
    }

    public static function set(string $key, $value): void
    {
        $hasValue     = Schema::hasColumn('settings', 'value');
        $hasValueJson = Schema::hasColumn('settings', 'value_json');
        $hasType      = Schema::hasColumn('settings', 'type');

        $payload = [];

        // Decide type
        $isScalar = is_bool($value) || is_scalar($value);
        $type = $isScalar ? 'string' : 'json';

        if ($hasValue) {
            if (is_bool($value))        $payload['value'] = $value ? '1' : '0';
            elseif (is_scalar($value))  $payload['value'] = (string)$value;
            else                        $payload['value'] = json_encode($value, JSON_UNESCAPED_UNICODE);
        }

        if ($hasValueJson) {
            $json = json_encode($value, JSON_UNESCAPED_UNICODE);
            if ($json === null) $json = '""'; // avoid NOT NULL
            $payload['value_json'] = $json;
        }

        if ($hasType) {
            $payload['type'] = $type;
        }

        if (!$payload) { // very old schema fallback
            $payload['value'] = is_scalar($value) ? (string)$value : json_encode($value, JSON_UNESCAPED_UNICODE);
        }

        static::updateOrCreate(['key'=>$key], $payload);
    }
}
