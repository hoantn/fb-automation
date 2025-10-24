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
    public function listPages(FacebookService $fb)
    {
        $user  = Auth::user();
        $pages = $fb->listManagedPages($user);

        return view('pages.connect', compact('pages'));
    }

    public function connect(Request $request, FacebookService $fb)
    {
        $v = Validator::make($request->all(), [
            'meta_page_id'  => 'required|string',
            'name'          => 'required|string',
            'access_token'  => 'nullable|string',
        ]);
        $v->validate();

        $page = Page::updateOrCreate(
            ['meta_page_id' => $request->input('meta_page_id')],
            [
                'name'         => $request->input('name'),
                'access_token' => $request->input('access_token') ?: null,
            ]
        );

        $token = $request->input('access_token');
        if (!$token) {
            $apiPages = collect($fb->listManagedPages(Auth::user()))->keyBy('id');
            if ($apiPages->has($page->meta_page_id)) {
                $token = Arr::get($apiPages[$page->meta_page_id], 'access_token');
            }
        }

        if ($token) {
            $fb->ensurePageToken(
                $page,
                (string)$token,
                scopes: null,
                issuedByUserId: Auth::id(),
                status: 'active',
                expiresAt: null
            );
        }

        if (method_exists(Auth::user(), 'pages')) {
            Auth::user()->pages()->syncWithoutDetaching([
                $page->id => ['role' => 'owner']
            ]);
        }

        $ok = $fb->subscribePageEvents($page);

        return redirect()->route('home')
            ->with('status', $ok ? 'Đã liên kết & subscribe!' : 'Đã lưu page, nhưng subscribe thất bại. Vui lòng kiểm tra quyền/token.');
    }
}
