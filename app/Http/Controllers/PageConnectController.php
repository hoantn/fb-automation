<?php

namespace App\Http\Controllers;

use App\Models\Page;
use App\Services\FacebookService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Arr;

class PageConnectController extends Controller
{
    /** Hiển thị list page từ /me/accounts */
    public function listPages(FacebookService $fb)
    {
        $user  = Auth::user();
        $pages = $fb->listManagedPages($user);

        return view('pages.connect', compact('pages'));
    }

    /** Nhận POST connect từ UI: meta_page_id, name, access_token (optional) */
    public function connect(Request $request, FacebookService $fb)
    {
        $v = Validator::make($request->all(), [
            'meta_page_id'  => 'required|string',
            'name'          => 'required|string',
            'access_token'  => 'nullable|string',
        ]);
        $v->validate();

        // Lưu/ cập nhật Page
        $page = Page::updateOrCreate(
            ['meta_page_id' => $request->input('meta_page_id')],
            [
                'name'         => $request->input('name'),
                'access_token' => $request->input('access_token') ?: null,
            ]
        );

        // Token ưu tiên: từ request; nếu không có -> lấy lại từ /me/accounts
        $token = $request->input('access_token');
        if (!$token) {
            $apiPages = collect($fb->listManagedPages(Auth::user()))->keyBy('id');
            if ($apiPages->has($page->meta_page_id)) {
                $token = Arr::get($apiPages[$page->meta_page_id], 'access_token');
            }
        }

        // Ghi kép token (pages + page_tokens)
        if ($token) {
            $fb->ensurePageToken(
                $page,
                (string) $token,
                scopes: null,              // /me/accounts không trả scopes – để null
                issuedByUserId: Auth::id(),
                status: 'active',
                expiresAt: null
            );
        }

        // Auto subscribe
        $ok = $fb->subscribePageEvents($page);

        return redirect()
            ->route('pages.connect')
            ->with('status', $ok ? 'Đã liên kết & subscribe!' : 'Đã lưu page, nhưng subscribe thất bại. Vui lòng kiểm tra quyền hoặc token.');
    }
}
