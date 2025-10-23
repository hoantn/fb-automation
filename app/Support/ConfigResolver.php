<?php

namespace App\Support;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

final class ConfigResolver
{
    public function get(string $key, array $ctx = [], $default = null): mixed
    {
        $cacheKey = 'cfg:' . md5($key . json_encode($ctx));
        return Cache::remember($cacheKey, 1800, function () use ($key, $ctx, $default) {
            $order = [
                ['scope_type' => 'user', 'id' => $ctx['user_id'] ?? null],
                ['scope_type' => 'page', 'id' => $ctx['page_id'] ?? null],
                ['scope_type' => 'workspace', 'id' => $ctx['workspace_id'] ?? null],
                ['scope_type' => 'plan', 'id' => $ctx['plan_id'] ?? null],
                ['scope_type' => 'global', 'id' => null],
            ];
            foreach ($order as $s) {
                if ($s['scope_type'] !== 'global' && empty($s['id'])) {
                    continue;
                }
                $q = Setting::query()
                    ->where('key', $key)
                    ->where('scope_type', $s['scope_type'])
                    ->when($s['scope_type'] !== 'global', fn($qq) => $qq->where('scope_id', $s['id']))
                    ->where('is_active', true)
                    ->orderByDesc('version')
                    ->first();
                if ($q) return $this->cast($q->type, $q->value_json);
            }
            return $default;
        });
    }

    private function cast($type, $val)
    {
        $v = $val;
        return match ($type) {
            'boolean' => (bool)$v,
            'number'  => (float)$v,
            default   => $v,
        };
    }
}
