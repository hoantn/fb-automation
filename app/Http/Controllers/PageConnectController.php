<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\FacebookService;
use App\Models\Page;
use App\Models\PageToken;
use Illuminate\Support\Facades\Schema;

class PageConnectController extends Controller
{
    public function listPages(FacebookService $fb)
    {
        $user = Auth::user();
        $pages = $fb->listManagedPages($user);
        return view('pages.connect', compact('pages'));
    }

    public function connect(Request $request, FacebookService $fb)
    {
        $data = $request->validate([
            'page_id'           => 'required|string',
            'name'              => 'nullable|string',
            'access_token'      => 'nullable|string',
            'scopes'            => 'nullable|string',
            'issued_by_user_id' => 'nullable|string',
            'status'            => 'nullable|string',
        ]);

        $page = Page::updateOrCreate(
            ['meta_page_id' => $data['page_id']],
            [
                'name' => $data['name'] ?? $data['page_id'],
                'access_token' => $data['access_token'] ?? null,
            ]
        );

        if (Schema::hasTable('page_tokens') && !empty($data['access_token'])) {
            PageToken::create([
                'page_id' => $page->id,
                'access_token' => $data['access_token'],
                'scopes' => $data['scopes'] ?? null,
                'issued_by_user_id' => $data['issued_by_user_id'] ?? null,
                'status' => $data['status'] ?? 'active',
            ]);
        }

        if (Auth::check() && method_exists($page, 'users')) {
            $page->users()->syncWithoutDetaching([Auth::id() => ['role' => 'owner']]);
        }

        try {
            $fb->subscribePageEvents($page);
        } catch (\Throwable $e) {
            \Log::warning($e->getMessage());
        }

        return redirect()->route('home')->with('success', 'Kết nối Page thành công!');
    }
}
