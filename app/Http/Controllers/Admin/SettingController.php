<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting;

class SettingController extends Controller
{
    public function index()
    {
        $config = [
            'dashboard_refresh_interval' => (int)config('system.dashboard_refresh_interval', 5),
            'cache_duration'             => (int)config('system.cache_duration', 10),
            'auto_reply_enabled'         => (bool)config('system.auto_reply_enabled', true),
        ];

        return view('admin.settings', compact('config'));
    }

    public function update(Request $r)
    {
        $data = $r->validate([
            'dashboard_refresh_interval' => 'required|integer|min:3|max:60',
            'cache_duration'             => 'required|integer|min:5|max:120',
            'auto_reply_enabled'         => 'nullable|in:1',
        ]);

        Setting::set('dashboard_refresh_interval', (int)$data['dashboard_refresh_interval']);
        Setting::set('cache_duration',             (int)$data['cache_duration']);
        Setting::set('auto_reply_enabled',         isset($data['auto_reply_enabled']));

        return back()->with('status', 'Đã lưu cấu hình.');
    }
}
