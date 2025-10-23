<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Page;
use App\Services\FacebookService;

class PageConnectController extends Controller
{
    public function listPages(\App\Services\FacebookService $fb)
    {
        $pages = $fb->listManagedPages(Auth::user());
        return view('pages.connect', compact('pages'));
    }

    public function connect(Request $r)
    {
        $data = $r->validate([
            'meta_page_id' => 'required',
            'name' => 'required',
            'access_token' => 'required',
        ]);

        $page = Page::updateOrCreate(
            ['meta_page_id' => $data['meta_page_id']],
            ['name' => $data['name']]
        );

        $page->members()->syncWithoutDetaching([Auth::id() => ['role' => 'owner']]);

        $page->tokens()->create([
            'access_token'      => $data['access_token'],
            'scopes'            => ['pages_messaging','pages_read_engagement','pages_manage_metadata'],
            'issued_by_user_id' => Auth::id(),
            'status'            => 'active',
        ]);
		// === AUTO-SUBSCRIBE NGAY TẠI ĐÂY ===
		app(FacebookService::class)->subscribePageEvents($page);
        return redirect('/')->with('status', 'Đã liên kết & subscribe!!');
    }
}
