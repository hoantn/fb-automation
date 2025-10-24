<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting;

class SettingController extends Controller
{
    public function index()
    {
        return view('admin.settings.index', [
            'dashboard_refresh_interval' => (int) Setting::get('dashboard_refresh_interval', 5),
            'cache_duration'             => (int) Setting::get('cache_duration', 10),
            'auto_reply_enabled'         => (bool) Setting::get('auto_reply_enabled', false),
        ]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'dashboard_refresh_interval' => ['required', 'integer', 'min:1', 'max:3600'],
            'cache_duration'             => ['required', 'integer', 'min:1', 'max:86400'],
            'auto_reply_enabled'         => ['nullable'],
        ]);

        Setting::set('dashboard_refresh_interval', (int) $data['dashboard_refresh_interval']);
        Setting::set('cache_duration', (int) $data['cache_duration']);
        Setting::set('auto_reply_enabled', (bool) $request->boolean('auto_reply_enabled'));

        return back()->with('success', 'Đã lưu cấu hình.');
    }
}
