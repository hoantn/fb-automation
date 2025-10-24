<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting;

class SettingController extends Controller
{
    public function index()
    {
        return view('admin.settings', [
            'dashboard_refresh_interval' => Setting::get('dashboard_refresh_interval', 5),
            'cache_duration'             => Setting::get('cache_duration', 10),
            'auto_reply_enabled'         => (bool) Setting::get('auto_reply_enabled', false),
        ]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            '_token'                     => 'required',
            'dashboard_refresh_interval' => 'nullable|integer|min:1|max:3600',
            'cache_duration'             => 'nullable|integer|min:1|max:86400',
            'auto_reply_enabled'         => 'nullable|in:0,1,on,off,true,false',
        ]);

        Setting::set('dashboard_refresh_interval', (int) ($request->input('dashboard_refresh_interval', 5)));
        Setting::set('cache_duration', (int) ($request->input('cache_duration', 10)));
        Setting::set('auto_reply_enabled', (bool) ($request->boolean('auto_reply_enabled')));

        return back()->with('success', 'Đã lưu cấu hình.');
    }
}
